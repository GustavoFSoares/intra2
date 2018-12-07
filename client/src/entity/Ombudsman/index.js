const OmbudsmanUser = require('./OmbudsmanUser').default
export default class Ombudsman {
    constructor(ombudsman = { group: '', ombudsman: '', registerTime: '', ombudsmanUser: new OmbudsmanUser(), }) {
        this.id = ombudsman.id
        this.ombudsmanUser = new OmbudsmanUser(ombudsman.ombudsmanUser)
        this.ombudsmanUserDescription = ombudsman.ombudsmanUserDescription
        this.ombudsmanUserSugestion = ombudsman.ombudsmanUserSugestion
        this.ombudsman = ombudsman.ombudsman ? ombudsman.ombudsman : window.$session.get('user')
        this.ombudsmanDescription = ombudsman.ombudsmanDescription
        
        this.manager = ombudsman.manager
        this.managerResponse = ombudsman.managerResponse
        
        this.origin = ombudsman.origin
        this.demands = ombudsman.demands ? ombudsman.demands : []
        this.type = ombudsman.type

        this.bed = ombudsman.bed // *
        this.group = ombudsman.group // *
        
        this.responseToUser = ombudsman.responseToUser
        this.registerTime = ombudsman.registerTime ? ombudsman.registerTime : new Date()
        this.reported = ombudsman.reported ? true : false
        this.answered = ombudsman.answered ? true : false
    }
}
