import axios from 'axios';
/** Reserved for future JSON interactions; current public/admin workflows use Blade session forms. */
export const api = axios.create({ baseURL: '/api', timeout: 12000, headers: { Accept: 'application/json' }, withCredentials: true });
export const apiContract = { mode: 'server-rendered', asynchronousResources: [] };
