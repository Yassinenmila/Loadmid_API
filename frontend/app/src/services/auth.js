import axios from 'axios'

const API = axios.create({
  baseURL: 'http://localhost:8000/api'
})

// Ajouter automatiquement le token dans les requêtes
API.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export const login = (data) => API.post('/login', data)

export const register = (data) => API.post('/register', data)

export const logout = () => API.post('/logout')

export const getUser = () => API.get('/user')
