import Vue from 'vue'
import Router from 'vue-router'
import local from '../components/screens/local'
import edit from '../components/screens/edit'

Vue.use(Router)

export default new Router({
	routes: [
		{
			path: '/',
			name: 'local',
			component: local
		},
		{
			path: '/editor/:id/:file?',
			name: 'edit',
			component: edit
		}
	]
})
 