export default class User {
        constructor(user = null) {
            if(!user){
                user = { group: { name: '', enterprise: '' } }
            }
            this.id = user.id
            this.code = user.code
            this.name = user.name
            this.email = user.email
            this.group = user.group
            this.level = user.level
            this.admin = user.admin ? true : false
            this.ramal = user.ramal
            this.occupation = user.occupation
            this.active = user.active ? true : false
            this.c_removed = user.c_removed ? true : false
            this.imageAccess = user.imageAccess ? true : false
        }
}
