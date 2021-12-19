import { useState } from 'react';

export default function isExpired() {
    const getToken = () => {
        const token = localStorage.getItem('access-token');
        return token
    };

    try {
        let exp = JSON.parse(atob(getToken().split(".")[1])).exp;
        if (exp * 1000 < Date.now())
            return true;
        else
            return false;
    } catch (e) {
        return null;
    }

}