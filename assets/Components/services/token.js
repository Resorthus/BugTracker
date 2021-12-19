import { useState } from 'react';

export default function useToken() {
    const getToken = () => {
        const token = localStorage.getItem('access-token');
        return token
    };

    const [token, setToken] = useState(getToken());

    const saveToken = data => {
        if(data != null) {
            localStorage.setItem('access-token', data.token);
            setToken(data.token);
        }
        else {
            localStorage.clear();
            setToken(null);
        }
    };

    return {
        setToken: saveToken,
        token
    }
}