import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"
import DatePicker from "react-datepicker";

export default function Register({setShowRegisterModal, showRegisterModal, setSuccess})
{
    const {token, seToken} = useToken();

    const [email, setEmail] = useState();
    const [password, setPassword] = useState();
    const [repeatPassword, setRepeatPassword] = useState();
    const [role, setRole] = useState("ROLE_PROGRAMMER");
    const [firstName, setFirstName] = useState();
    const [lastName, setLastName] = useState();
    const [birthdate, setBirthdate] = useState();
    const [level, setLevel] = useState();
    const [specialization, setSpecialization] = useState();
    const [technology, setTechnology] = useState();
    const [error, setError] = useState(null);

    const [isProgrammer, setIsProgrammer] = useState(true);

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: email,
                password: password,
                repeatpassword: repeatPassword,
                role: role,
                firstname: firstName,
                lastname: lastName,
                birthdate: birthdate,
                level: (isProgrammer ? level : null),
                specialization : (isProgrammer ? specialization : null),
                technology : (isProgrammer ? technology : null)
            })
        };
        
        fetch("http://localhost:8000/register", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setShowRegisterModal(false);
                    setSuccess("Registration Successful! You may now log in");
                }
            )
    }

    const handleRole = (roleChange) => {
        setRole(roleChange);
        if (roleChange == "ROLE_PROGRAMMER")
        {
            setIsProgrammer(true);
        }
        else
        {
            setIsProgrammer(false);
        }
    }



    return (
        <>
            <Modal show={showRegisterModal}>
                <Modal.Header>Sukurti naują projektą</Modal.Header>
                <Modal.Body>
                    <form onSubmit={handleSubmit}>

                        <div className="form-group">
                            <label>Email</label>
                            <input type="text" onChange={e => setEmail(e.target.value)} className="form-control" placeholder="Email"/>
                        </div>

                        <div className="form-group">
                            <label>Password</label>
                            <input type="password" onChange={e => setPassword(e.target.value)} className="form-control" placeholder="Password"/>
                        </div>

                        <div className="form-group">
                            <label>Repeat Password</label>
                            <input type="password" onChange={e => setRepeatPassword(e.target.value)} className="form-control" placeholder="Repeat Password"/>
                        </div>

                        <div className="form-group">
                            <label>Role</label>
                            <select className="form-control" onChange={e => handleRole(e.target.value)}>
                                <option selected="selected" value="ROLE_PROGRAMMER">Programmer</option>
                                <option value="ROLE_SUPERVISOR">Supervisor</option>
                            </select>
                        </div>

                        <div className="form-group">
                            <label>First Name</label>
                            <input type="text" onChange={e => setFirstName(e.target.value)} className="form-control" placeholder="First Name"/>
                        </div>

                        <div className="form-group">
                            <label>Last Name</label>
                            <input type="text" onChange={e => setLastName(e.target.value)} className="form-control" placeholder="Last Name"/>
                        </div>

                        <div className="form-group">
                            <label>Birthdate</label><br/>
                            <DatePicker
                                className="form-control"
                                placeholder="Birthdate"
                                selected={birthdate}
                                onChange={(date) => setBirthdate(new Date(date))}
                                showTimeSelect
                                timeIntervals={15}
                                timeCaption="time"
                            />
                        </div>

                        {
                            isProgrammer &&
                            <>

                            <div className="form-group">
                                <label>Level</label>
                                <input type="text" onChange={e => setLevel(e.target.value)} className="form-control" placeholder="Level"/>
                            </div>

                            <div className="form-group">
                            <label>Specialization</label>
                            <input type="text" onChange={e => setSpecialization(e.target.value)} className="form-control" placeholder="Specialization"/>
                            </div>

                            <div className="form-group">
                            <label>Technology</label>
                            <input type="text" onChange={e => setTechnology(e.target.value)} className="form-control" placeholder="Technology"/>
                            </div>

                            </>
                        }
                    </form>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={handleSubmit}>Register</Button> {' '}
                    <div><Button onClick={() => setShowRegisterModal(false)}>Close</Button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    )

}