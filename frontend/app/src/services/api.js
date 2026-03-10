import axios from 'axios'

// On crée une instance Axios avec l'URL de base
const API = axios.create({
  baseURL: 'http://localhost:8000/api'
})

// Fonction pour récupérer le message depuis l'endpoint /hello
export const getHello = () => API.get('/hello')
