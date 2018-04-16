import http from '../client'

export const getLinks = () => {
    return http.get(`/link/`).then( res => res.data )
}

export const getLink = ($id) => {
    return http.get(`/link/${id}`).then( res => res.data )
}

// export const addContact = (contact) => {
//     return http.post(`/add`, contact).then( res => res.data )
// }

// export const getContact = (id) => {
//     return http.get(`/find/${id}`).then( res => res.data )
// }

// export const editContact = (id, contact) => {
//     return http.put(`/edit/${id}`, contact).then( res => res.data )
// }

// export const deleteContact = (id) => {
//     return http.post(`/delete/${id}`).then(res => res.data)
// }

// export const insertDefaultContacts = () => {
//     return http.get(`/insert`).then(res => res.data)
// }