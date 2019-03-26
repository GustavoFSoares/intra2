import Vue from 'vue'
import Router from 'vue-router'

import Home from '@/components/Home.vue'
import Teste from '@/components/Teste.vue'
import NotFound from '@/components/NotFound.vue'
import DontHavePermission from '@/components/DontHavePermission.vue'

import adminManagerRoutes from './admin-manager-routes'
import adverseEventsRoutes from './adverse-events-routes'
import alertsRoutes from './alerts-routes'
import covenantRoutes from './covenants-routes'
import duplicatedUsersRoutes from './duplicated-users-routes'
import eletronicDocumentsRoutes, { eletronicDocumentsStatusRoutes, eletronicDocumentsTypesRoutes } from './eletronic-documents-routes'
import homeUserRoutes from './home-user-routes'
import incidentReportingRoutes from './incident-reporting-routes'
import loginRoutes from './login-routes'
import linkRoutes from './link-routes'
import modulesRoutes from './modules-routes'
import ombudsmanRoutes, { ombudsmanDemandsRoutes, ombudsmanOriginsRoutes } from './ombudsman-routes/'
import popsRoutes from './pops-routes'
import ramalsRoutes from './ramals-routes'
import rotinesRoutes from './rotines-routes'
import { trainingRoutes, roomTrainingRoutes, collaboratorsRoutes } from './teaching-and-research-routes'
import userRoutes from './user-routes'

import Access from "@/entity/Access";

const routes = [
    {
        path: `/`,
        component: Home
    },
    {
        path: '*',
        component: NotFound
    },
    {
        path: '/teste',
        component: Teste
    },
    {
        path: '/sem-permissao', 
        name: 'no-permission',
        component: DontHavePermission,
    }
]

const Routes = new Router({
    routes: routes,
    mode: 'history',
})
Routes.addRoutes(adminManagerRoutes)
Routes.addRoutes(adverseEventsRoutes)
Routes.addRoutes(alertsRoutes)
Routes.addRoutes(collaboratorsRoutes)
Routes.addRoutes(covenantRoutes)
Routes.addRoutes(duplicatedUsersRoutes)
Routes.addRoutes(eletronicDocumentsRoutes)
Routes.addRoutes(eletronicDocumentsStatusRoutes)
Routes.addRoutes(eletronicDocumentsTypesRoutes)
Routes.addRoutes(homeUserRoutes)
Routes.addRoutes(incidentReportingRoutes)
Routes.addRoutes(loginRoutes)
Routes.addRoutes(linkRoutes)
Routes.addRoutes(modulesRoutes)
Routes.addRoutes(ombudsmanRoutes)
Routes.addRoutes(ombudsmanDemandsRoutes)
Routes.addRoutes(ombudsmanOriginsRoutes)
Routes.addRoutes(popsRoutes)
Routes.addRoutes(ramalsRoutes)
Routes.addRoutes(roomTrainingRoutes)
Routes.addRoutes(rotinesRoutes)
Routes.addRoutes(trainingRoutes)
Routes.addRoutes(userRoutes)

Routes.beforeEach((to, from, next) => {
    const Session = (window.sessionStorage.getItem('vue-session-key') != '{}') ?
        JSON.parse(window.sessionStorage.getItem('vue-session-key')) : false
    
    if (to.meta.requiresAuth) {
        window.lastRouteAccess = to.path

        if (!Session) {
            next({ path: `/login` })
        } else {
            
            let access = new Access()
            
            access.id = Session.user.id
            access.path = to.path
            
            
            if (to.meta.groupAuth) {
                let ok = false
                to.meta.groupAuth.forEach(group => {
                    if (ok != true) {
                        if (group == Session.user.group.groupId) {
                            ok = true
                            access.permissions = access.permissions.splice('group', 0)
                        } else {
                            access.permissions['group'] = false
                        }
                    }
                })
            }
            if (to.meta.levelAuth) {
                
                if (!(to.meta.levelAuth <= Session.user.level)) {
                    access.permissions['level'] = false
                }
            }
            if (to.meta.adminAuth) {
                if (to.meta.adminAuth !== Session.user.admin) {
                    access.permissions['admin'] = false
                }
            }
            
            if(access.isValid()) {
                next()
            } else {
                window.access = access
                next({ name: `no-permission` })
            }
            
        }
    } else {
        next()
    }
})

Vue.use(Router)
export default Routes