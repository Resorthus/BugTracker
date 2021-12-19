import React, {Component, useState} from 'react';
import PropTypes from "prop-types";
import Register from "./register";

async function loginUser(credentials) {
    return fetch('http://localhost:8000/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(credentials)
    })
        .then(data => data.json())
}

export default function Login({setToken}) {
    const [username, setUsername] = useState();
    const [password, setPassword] = useState();
    const [errors, setErrors] = useState();
    const [showRegisterModal, setShowRegisterModal] = useState(false);
    const [success, setSuccess] = useState(null);


    const handleSubmit = async e => {
        e.preventDefault();
        const data = await loginUser({
            username,
            password
        });
        if('code' in data) {
            setErrors('Incorrect credentials');
            return;
        }
        setToken(data);
    }

    const handleRegisterModal = () => {
        setShowRegisterModal(true);
    }

    return (

        <>
        <div className="container border rounded" style={{width: '500px',
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%,-50%)',
            padding: '7px'
        }}>
            {success &&
            <div className="alert alert-success" role="alert">
                <b>{success}</b>
            </div>
            }
            <h5 style={{borderBottom: '1px solid #ced4da', paddingBottom: '4px'}}>Login</h5>
            {errors && <li className="text-danger">{errors}</li>}
            <form onSubmit={handleSubmit}>
                <div className="form-group">
                    <label htmlFor="exampleInputEmail1">Email</label>
                    <input type="text" onChange={e => setUsername(e.target.value)} className="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Email" />
                </div>
                <div className="form-group">
                    <label htmlFor="exampleInputPassword1">Password</label>
                    <input type="password" onChange={e => setPassword(e.target.value)} className="form-control" id="exampleInputPassword1" placeholder="Password" /><br/>
                </div>
                <button type="submit" className="btn btn-primary">Login</button>
                &nbsp;
                <button type="button" onClick={handleRegisterModal} className="btn btn-primary">Register</button>
            </form>
        </div>

            {
                showRegisterModal &&
                <Register setShowRegisterModal={setShowRegisterModal}
                          showRegisterModal={showRegisterModal}
                          setSuccess={setSuccess}
                />
            }

        </>


    )
}

Login.propTypes = {
    setToken: PropTypes.func.isRequired
};