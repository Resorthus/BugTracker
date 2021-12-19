import {Navigate} from 'react-router-dom';
export default function logout(){
    localStorage.removeItem('access-token');
    return <Navigate to='/'  />
}