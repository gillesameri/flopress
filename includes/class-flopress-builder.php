<?php
/**
 * The builder-specific functionality of the plugin.
 *
 * @link       https://flopress.io
 * @since      1.1.8
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 */

/**
 * The builder-specific functionality of the plugin.
 *
 * @package    Flopress
 * @subpackage Flopress/includes
 * @author     Flopress team <team@flopress.io>
 */


class Flopress_Builder {

	/**
	 * The list of files.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      array    $files    The list of files.
	 */
    private $files = array();

	/**
	 * The twig instance.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      object    $twig    The twig instance.
	 */
    private $twig = false;

	/**
	 * The loader.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      string    $loader    The loader.
	 */
    private $loader = false;

	/**
	 * The list of components.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      array    $components   The list of components.
	 */
    private $components = array();

	/**
	 * The list of links.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      array    $links    The list of links.
	 */
    private $links = array();

	/**
	 * The references to variable temp name.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      array    $files_references    The references to variable temp name.
	 */
    private $files_references = array();

	/**
	 * The build id.
	 *
	 * @since    1.1.8
	 * @access   private
	 * @var      string    $build       The build id.
	 */
    private $build;

	/**
	 * Define the builder functionality of the plugin.
	 *
	 * Init the builder.
	 * 
	 * @since    1.1.8
	 */
    public function __construct($build) 
    {
        $this->build = $build;
        $this->files = '';
        $this->loader = new Twig_Loader_Filesystem(__DIR__.'/templates');
        $this->twig = new Twig_Environment($this->loader, array(
            'debug' => true,
        ));
        $this->twig->addFilter( new Twig_SimpleFilter('cast_to_array', function ($stdClassObject) {
            $response = array();
            foreach ($stdClassObject as $key => $value) {
                $response[$key] = $value;
            }
            return $response;
        }));
        $this->twig->addExtension(new Twig_Extension_Debug());
    }
    
	/**
	 * Build feature.
	 *
	 * @since    1.1.8
	 * @param    array                $files            The list of files in features
	 */
    public function build($files)
    {
        try {
			$this->files = json_decode(json_encode($files));

            $statics = $blocks = array();

            foreach($this->files as $k => $v){
                if(!in_array($v->type,array('action','filter','shortcode'))){
                    if(!array_key_exists($v->type,$this->files_references)) $this->files_references[$v->type] = array();
                    $uuid = uniqid('fpv_');
                    $this->files_references[$v->type][$k] = array('content' => $uuid);
                    if($v->type == 'data'){
                        if(is_string($v->content->value)) $v->content->value = addcslashes($v->content->value,'"');
                    }
                    else{
                        if(is_string($v->content)) $v->content = addcslashes($v->content,'"');
                    }
                    $v->var_name = $uuid;
                    $statics[$k] = $v;
                }
            }

            $canCompile = array();
            foreach($this->files as $k => $v){
                if(in_array($v->type,array('action','filter','shortcode'))){
                    if(property_exists($v->content,'components')){
                        foreach($v->content->components as $id => $component){
                            if(($component->type == 'fct' || $component->type == 'efct') && !function_exists($component->name)){
                                $canCompile[] = $component->name;
                            }
                        }
                    }
                }
            }

            if(count($canCompile) > 0){
                return array('status' => false, 'message' => 'Component(s) '.implode(', ',$canCompile).' not available in your wordpress installation.');
            }

            foreach($this->files as $k => $v){
                if(in_array($v->type,array('action','filter','shortcode'))){
                    if(property_exists($v->content->components,'hook_args')){
                        $hook_args = $v->content->components->hook_args;
                        $fct_name = $hook_args->name;
                        if(array_key_exists('inputs',$hook_args)){
                            $inputLength = count((array) $hook_args->inputs);
                            $inputs = array();
                            if($inputLength > 0){
                                foreach($hook_args->inputs as $ki => $input){
                                    $sk = '{$'.$ki.'}';
                                    $inputs[$sk] = (property_exists($input,'value')) ? $input->value : false;
                                }
                                $fct_name = str_replace(array_keys($inputs),$inputs,$fct_name);
                            }
                        }
                        
                        if(!array_key_exists($v->type,$this->files_references)) $this->files_references[$v->type] = array();
                        $this->files_references[$v->type][$k] = array('name' => $fct_name, 'callable' => uniqid('fpf_'));
                    }
                }

            }

            foreach($this->files as $k => $v){
                if(in_array($v->type,array('action','filter','shortcode'))){
                    if(property_exists($v->content->components,'hook_args')){
                        $blocks[$k] = $this->build_hook($k,$v);
                    }
                }
            }

            $output = $this->twig->render(
                'feature.twig', 
                array(
                    'class_name' => $this->build, 
                    'statics' => $statics, 
                    'blocks' => $blocks
                ) 
            );

            ob_start();
            $evalResult = @eval('?' . '>' .$output);
            ob_end_clean();
            
            if($evalResult !== FALSE) {
                return array('status' => true, 'message' => $output);
            } else {
                return array('status' => false, 'message' => 'Code not working. Variables mismatch. Build canceled.');
            }
		} catch(Throwable $e) {
            return array('status' => false, 'message' => 'Error during building. Variables mismatch. Build canceled.');
		} catch(\Exception $e) {
            return array('status' => false, 'message' => 'Error during building. Variables mismatch. Build canceled.');
		}
        
    }
    
    /**
	 * Build hook.
	 *
	 * @since    1.1.8
	 * @param    int                  $id               The ID of the hook.
	 * @param    object               $hook             The hook content.
	 */
    public function build_hook($id,$hook){
        $this->files_references['local'] = array();
        
        $this->components = $hook->content->components;
        $this->links = $hook->content->links;

        $hook_args = $hook->content->components->hook_args;
        
        $fct_name = $hook_args->name;
        if(array_key_exists('inputs',$hook_args)){
            $inputLength = count((array) $hook_args->inputs);
            $inputs = array();
            if($inputLength > 0){
                foreach($hook_args->inputs as $k => $input){
                    $sk = '{$'.$k.'}';
                    $inputs[$sk] = (property_exists($input,'value')) ? $input->value : false;
                }
                $fct_name = str_replace(array_keys($inputs),$inputs,$fct_name);
            }
        }

        $this->files_references[$hook->type][$id]['name'] = $fct_name;

        $hook->name = $this->files_references[$hook->type][$id]['name'];
        $hook->fct_name = $this->files_references[$hook->type][$id]['callable'];

        $hook->args = array();
        $this->files_references['local']['hook_args'] = array();
        foreach($hook_args->outputs as $connectorId => $connector){
            $uuid = uniqid('$fpv_');
            $this->files_references['local']['hook_args'][$connectorId] = $uuid;
            if($connector->type != 'efct') $hook->args[] = $uuid;
        }
        
        $hook->args_length = count($hook->args);
        $hook->content->priority = ($hook->content->priority) ? $hook->content->priority : 10;
        
        $hook->blocks = $this->build_blocks('hook_args','ofct');
        
        return $hook;
    }

    /**
	 * Get references to variables.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 * @param    string               $references       The references to variables names.
	 */
    public function get_reference($componentId, $connectorId, $references = array()){
        if($componentId && $connectorId){
            $component = $this->getComponent($componentId);
            
            if(in_array($component->type, array('action','filter','shortcode','data','template'))){
                return $this->get_global_reference($componentId, $connectorId);
            }
            else{
                return $this->get_local_reference($componentId, $connectorId, $references);
            }
        }
        return 'false';
    }

    /**
	 * Get references to global files.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 */
    public function get_global_reference($componentId, $connectorId){
        $component = $this->getComponent($componentId);

        if(!array_key_exists($component->type,$this->files_references))
                $this->files_references[$component->type] = array();
            
        if(!array_key_exists($component->ref, $this->files_references[$component->type]))
            $this->files_references[$component->type][$component->ref][$connectorId] = uniqid('$fpv_');

            
        if(in_array($component->type, array('action','filter','shortcode'))){
            if($connectorId == 'callable'){
                return 'array($this,"'.$this->files_references[$component->type][$component->ref][$connectorId].'")';
            }
            else{
                return '"'.$this->files_references[$component->type][$component->ref][$connectorId].'"';
            }
        }
        elseif(in_array($component->type, array('data'))){
            return '$this->'.$this->files_references[$component->type][$component->ref][$connectorId];
        }
        elseif(in_array($component->type, array('template'))){
            $args = $this->build_args($componentId);
            $template_var = $this->files_references[$component->type][$component->ref][$connectorId];
            if((implode(',',$args) == 'false') || count($args) == 0){
                return '$this->'.$template_var.'';
            }
            else{
                return 'Flopress_Render::template($this->'.$template_var.','.implode(',',$args).')';
            }
        }
        return 'false';
    }

	/**
	 * Get references to local files.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 * @param    string               $references       The references to variables names.
	 */
    public function get_local_reference($componentId, $connectorId, $references){
        $component = $this->getComponent($componentId);

        if(!array_key_exists($componentId,$this->files_references['local']))
        $this->files_references['local'][$componentId] = array();
        
        if(!array_key_exists($connectorId, $this->files_references['local'][$componentId]))
        $this->files_references['local'][$componentId][$connectorId] = uniqid('$fpv_');

        if(in_array($component->type, array('efct','variable'))){
            return $this->files_references['local'][$componentId][$connectorId];
        }
        elseif(in_array($component->type, array('fct'))){
            $args = $this->build_args($componentId);
            return $component->name.'('.implode(',',$args).')';
        }
        elseif(in_array($component->type, array('hook-action','hook-filter','hook-shortcode'))){
            return $this->files_references['local'][$componentId][$connectorId];
        }
        elseif(in_array($component->type, array('instruction'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'get_array_value':
                    if(array_key_exists('array',$args)){
                        return $args['array'].'['.$args['param_arr'].']';
                    }
                    else{
                        return '';
                    }
                break;
                case 'get_object_property':
                    return $args['object'].'->{'.$args['property'].'}';
                break;
                case 'concat':
                    return '('.implode('.',$args).')';
                break;
                default:
                    return $this->files_references['local'][$componentId][$connectorId];
                break;
            }
        }
        elseif(in_array($component->type, array('logical'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'not':
                    return '!'.implode(',',$args).'';
                break;
                case 'and':
                    return '('.implode(' && ',$args).')';
                break;
                case 'or':
                    return '('.implode(' && ',$args).')';
                break;
            }
            return 'false';
        }
        elseif(in_array($component->type, array('arithmetic'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'addition':
                    return '('.implode(' + ',$args).')';
                break;
                case 'division':
                    return '('.implode(' / ',$args).')';
                break;
                case 'subtraction':
                    return '('.implode(' - ',$args).')';
                break;
                case 'multiplication':
                    return '('.implode(' * ',$args).')';
                break;
                case 'modulo':
                    return '('.implode(' % ',$args).')';
                break;
                case 'exponentiation':
                    return '('.implode(' ** ',$args).')';
                break;
            }
        }
        elseif(in_array($component->type, array('comparison'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'equals':
                    return '('.implode(' == ',$args).')';
                break;
                case 'not_equals':
                    return '('.implode(' != ',$args).')';
                break;
                case 'less_than':
                    return '('.implode(' < ',$args).')';
                break;
                case 'less_than_or_equal_to':
                    return '('.implode(' <= ',$args).')';
                break;
                case 'greater_than':
                    return '('.implode(' > ',$args).')';
                break;
                case 'greater_than_or_equal_to':
                    return '('.implode(' >= ',$args).')';
                break;
            }
        }
        elseif(in_array($component->type, array('cast'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'to_object':
                    return '(object) ('.implode('',$args).')';
                break;
                case 'to_array':
                    return '(array) ('.implode('',$args).')';
                break;
                case 'to_int':
                    return '(int) ('.implode('',$args).')';
                break;
                case 'to_string':
                    return '(string) ('.implode('',$args).')';
                break;
                case 'to_bool':
                    return '(bool) ('.implode('',$args).')';
                break;
                case 'to_float':
                    return '(float) ('.implode('',$args).')';
                break;
            }
        }
        elseif(in_array($component->type, array('global'))){
            $args = $this->build_args($componentId);
            switch($component->name){
                case 'get':
                    return '$_GET['.implode('',$args).']';
                break;
                case 'post':
                    return '$_POST'.implode('',$args).']';
                break;
                case 'server':
                    return '$_SERVER['.implode('',$args).']';
                break;
                case 'files':
                    return '$_FILES['.implode('',$args).']';
                break;
                case 'session':
                    return '$_SESSION['.implode('',$args).']';
                break;
                case 'cookie':
                    return '$_COOKIE['.implode('',$args).']';
                break;
            }
        }
        return 'false';
    }

	/**
	 * Build arguments value.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId          The id of the component.
	 */
    public function build_args($componentId){
        
        $component = $this->getComponent($componentId);
        $inputs = array();
        $defaultsVals = array();
        foreach($component->inputs as $inputId => $input){
            if($input->type != 'efct'){

                $comptVar = $this->getFromComponentId($componentId,$inputId);
                $connectVar = $this->getFromConnectorId($componentId,$inputId);
                $arg = $this->get_reference($comptVar,$connectVar);
                if($comptVar){
                    $inputs[$inputId] = $arg;
                }
                elseif(property_exists($input,'value') && $input->value !== false){
                    if(is_array($input->value) || is_object($input->value)){
                        $temp = array();
                        foreach( (array) $input->value as $kk => $kv) $temp[] = "'".$kk."' => '".$kv."'";
                        $inputs[$inputId] = "array(".implode(',',$temp).")";
                    }
                    else{
                        $inputs[$inputId] = "'".$input->value."'";
                    }
                }
                elseif(property_exists($input,'default') && $input->default){
                    $inputs[$inputId] = 'false';
                    $defaultsVals[$inputId] = $input->default;
                }
                else{
                    $inputs[$inputId] = 'false';
                }
            }
        }

        $canRemove = true;
        $tinputs = array_reverse($inputs);
        foreach($tinputs as $k => $input){
            if($input == 'false'){
                if($canRemove){
                    unset($inputs[$k]);
                }
                else{
                    $inputs[$k] = $defaultsVals[$k];
                }
            }
            else{
                $canRemove = false;
            }
        }
        return $inputs;
    }

	/**
	 * Build dependencies for initialization.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId          The id of the component.
	 * @param    int                  $connectorId          The id of the connector.
	 */
    public function build_dependencies($componentId, $connectorId){

        $fromComponentId = $this->getFromComponentId($componentId,$connectorId);
        $fromConnectorId = $this->getFromConnectorId($componentId,$connectorId);

        $component = $this->getComponent($fromComponentId);

        $inputs = false;
        if($component){
            $inputs = array(array('component' => $fromComponentId, 'connector' => $fromConnectorId));
            foreach($component->inputs as $inputId => $input){
                if($input->type != 'efct'){
                    $tmpInputs = $this->build_dependencies($fromComponentId, $inputId);
                    if($tmpInputs)
                        $inputs = array_merge($inputs, $tmpInputs);
                }
            }
        }
        return $inputs;
    }

 	/**
	 * Build block code render.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId          The id of the component.
	 * @param    int                  $connectorId          The id of the connector.
	 * @param    array                $referencesParents    The references to the parents variables already instancied.
	 */   
    public function build_blocks($componentId,$connectorId,$referencesParents=array()){
        $temp = array();
        $references = array();
        while($nextComponentId = $this->getToComponentId($componentId, $connectorId)){
            $component = $this->getComponent($nextComponentId);
            $args = $this->build_args($nextComponentId);
            $blocks = array();

            foreach($component->inputs as $inputId => $input){
                if($input->type != 'efct'){
                    if($dependencies = $this->build_dependencies($nextComponentId, $inputId)){
                        foreach($dependencies as $dependency){
                            $tcomponent = $this->getComponent($dependency['component']);
                            if(($tcomponent->type == 'variable')){
                                if(array_key_exists($dependency['component'].'-'.$dependency['connector'], $references)){
                                    $references[$dependency['component'].'-'.$dependency['connector']] = $references[$dependency['component'].'-'.$dependency['connector']] + 1;
                                }
                                elseif(!array_key_exists($dependency['component'].'-'.$dependency['connector'], $referencesParents)){
                                    
                                    $blocks[] = array(
                                        'id' => $dependency['component'],
                                        'connector' => $dependency['connector'],
                                        'type' => $tcomponent->type, 
                                        'name'=> $tcomponent->name, 
                                        'var' => $this->get_reference($dependency['component'],$dependency['connector']),
                                        'template' => 'components/'.$tcomponent->type.'.twig',
                                        'args' => $this->build_args($dependency['component']),
                                        'inputs' => $tcomponent->inputs,
                                        'value' => (property_exists($tcomponent, 'value') ? $tcomponent->value : 'false')
                                    );
                                    $references[$dependency['component'].'-'.$dependency['connector']] = 0;
                                }
                            }
                        }
                    }
                }
            }
            usort($blocks, function($a, $b) use ($references)
            {
                return strcmp($references[$a['id'].'-'.$a['connector']], $references[$b['id'].'-'.$b['connector']]);
            });
            $blocks = array_reverse($blocks);
            $temp = array_merge($temp,$blocks);
            
            switch($component->name){
                case 'if':
                    $children_true = $this->build_blocks($nextComponentId,'ofct_true',array_merge($referencesParents,$references));
                    $children_false = $this->build_blocks($nextComponentId,'ofct_false',array_merge($referencesParents,$references));
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,false), 
                        'ofct_true' => $children_true, 
                        'ofct_false' => $children_false,
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = false;
                    $connectorId = false;
                break;
                case 'while':
                    $children = $this->build_blocks($nextComponentId,'ofct_loop',array_merge($referencesParents,$references));
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,false), 
                        'ofct_loop' => $children,
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'foreach':
                    $children = $this->build_blocks($nextComponentId,'ofct_loop',array_merge($referencesParents,$references));
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,false), 
                        'key' => $this->get_reference($nextComponentId,'key_loop'), 
                        'item' => $this->get_reference($nextComponentId,'item_loop'), 
                        'ofct_loop' => $children,
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'for':
                    $children = $this->build_blocks($nextComponentId,'ofct_loop',array_merge($referencesParents,$references));
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,false), 
                        'index' => $this->get_reference($nextComponentId,'item_index'), 
                        'ofct_loop' => $children,
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'cascader':
                    $outputs = array();
                    foreach($component->outputs as $ko => $kv){
                        $outputs[$ko] = $this->build_blocks($nextComponentId,$ko,array_merge($referencesParents,$references));
                    }
                    $result = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,false), 
                        'template' => 'components/'.$component->name.'.twig',
                        'outputs' => $outputs,
                        'args' => $args
                    );
                    $result = array_merge($result,$outputs);
                    $temp[] = $result;

                    $componentId = false;
                    $connectorId = false;
                break;
                case 'return':
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => false, 
                        'template' => 'components/'.$component->name.'.twig',
                        'value' => $args['value']
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'unset':
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => false, 
                        'template' => 'components/'.$component->name.'.twig',
                        'variable' => $args['variable']
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'set_variable':
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => false, 
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'set_array_value':
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => false, 
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'set_object_property':
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => false, 
                        'template' => 'components/'.$component->name.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                case 'init_variable':
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
                default:
                    $temp[] = array(
                        'type' => $component->type, 
                        'name'=> $component->name, 
                        'var' => $this->get_reference($nextComponentId,'return'),
                        'template' => 'components/'.$component->type.'.twig',
                        'args' => $args
                    );
                    $componentId = $nextComponentId;
                    $connectorId = 'ofct';
                break;
            }
        }
        return $temp;
        
    }

	/**
	 * Get from component by ID.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 */
    public function getFromComponentId($componentId, $connectorId){
        $cmptId = false;
        foreach($this->links as $linkId => $link){
            if(($link->toComponent == $componentId) && ($link->toConnector == $connectorId)){
                $cmptId = $link->fromComponent;
            }
        }
        return $cmptId;
    }

    /**
	 * Get from connector by ID.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 */
    public function getFromConnectorId($componentId, $connectorId){
        $connectId = false;
        foreach($this->links as $linkId => $link){
            if(($link->toComponent == $componentId) && ($link->toConnector == $connectorId)){
                $connectId = $link->fromConnector;
            }
        }
        return $connectId;
    }

    /**
	 * Get to component by ID.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 * @param    int                  $connectorId      The id of the connector.
	 */
    public function getToComponentId($componentId, $connectorId){
        $cmptId = false;
        foreach($this->links as $linkId => $link){
            if(($link->fromComponent == $componentId) && ($link->fromConnector == $connectorId)){
                $cmptId = $link->toComponent;
            }
        }
        return $cmptId;
    }

	/**
	 * Get component by ID.
	 *
	 * @since    1.1.8
	 * @param    int                  $componentId      The id of the component.
	 */
    public function getComponent($componentId){
        if(property_exists($this->components, $componentId)){
            return $this->components->{$componentId};
        }
        return false;
    }


}
