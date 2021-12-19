import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"
import DatePicker from "react-datepicker";

export default function UpdateUser({user, showUpdateUserModal, setShowUpdateUserModal, setUser, setUsers, index, users})
{
    const {token, seToken} = useToken();
    const [isLoaded, setIsLoaded] = useState(false);

    const [updatedRole, setUpdatedRole] = useState(user.roles[0]);
    const [updatedFirstName, setUpdatedFirstName] = useState(user.FirstName);
    const [updatedLastName, setUpdatedLastName] = useState(user.LastName);
    const [updatedBirthdate, setUpdatedBirthdate] = useState(new Date(user.Birthdate));
    const [updatedLevel, setUpdatedLevel] = useState(user.Level);
    const [updatedSpecialization, setUpdatedSpecialization] = useState(user.Specialization);
    const [updatedTechnology, setUpdatedTechnology] = useState(user.Technology);

    const [isProgrammer, setIsProgrammer] = useState(user.roles[0] == "ROLE_PROGRAMMER");

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                role: updatedRole,
                firstname: updatedFirstName,
                lastname: updatedLastName,
                birthdate: updatedBirthdate,
                level: (isProgrammer ? updatedLevel : null),
                specialization : (isProgrammer ? updatedSpecialization : null),
                technology : (isProgrammer ? updatedTechnology : null)
            })
        };

        fetch("http://localhost:8000/api/users/" + user.id, requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setUser(data);
                    setIsLoaded(true);
                }
            )
    }

    const handleRole = (roleChange) => {
        setUpdatedRole(roleChange);
        if (roleChange == "ROLE_PROGRAMMER")
        {
            setIsProgrammer(true);
        }
        else
        {
            setIsProgrammer(false);
        }
    }

    useEffect(() => {
        if(isLoaded) {
            let usersCopy = users;
            usersCopy[index] = user;
            setUsers(usersCopy);
            setShowUpdateUserModal(false);
        }
    }, [isLoaded])



    return (
        <>
            <Modal show={showUpdateUserModal}>
                <Modal.Header>Edit User Info</Modal.Header>
                <Modal.Body>
                    <form onSubmit={handleSubmit}>

                        <div className="form-group">
                            <label>Role</label>
                            <select className="form-control" onChange={e => handleRole(e.target.value)}>
                                {
                                    user.roles[0] == "ROLE_PROGRAMMER" &&
                                    <>
                                    <option selected="selected" value="ROLE_PROGRAMMER">Programmer</option>
                                    <option value="ROLE_SUPERVISOR">Supervisor</option>
                                    </>
                                }
                                {
                                    user.roles[0] == "ROLE_SUPERVISOR" &&
                                    <>
                                        <option value="ROLE_PROGRAMMER">Programmer</option>
                                        <option selected="selected" value="ROLE_SUPERVISOR">Supervisor</option>
                                    </>
                                }
                            </select>
                        </div>

                        <div className="form-group">
                            <label>First Name</label>
                            <input type="text" value={updatedFirstName} onChange={e => setUpdatedFirstName(e.target.value)} className="form-control" placeholder="First Name"/>
                        </div>

                        <div className="form-group">
                            <label>Last Name</label>
                            <input type="text" value={updatedLastName} onChange={e => setUpdatedLastName(e.target.value)} className="form-control" placeholder="Last Name"/>
                        </div>

                        <div className="form-group">
                            <label>Birthdate</label><br/>
                            <DatePicker
                                className="form-control"
                                placeholder="Birthdate"
                                selected={updatedBirthdate}
                                onChange={(date) => setUpdatedBirthdate(new Date(date))}
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
                                    <input type="text" value={updatedLevel} onChange={e => setUpdatedLevel(e.target.value)} className="form-control" placeholder="Level"/>
                                </div>

                                <div className="form-group">
                                    <label>Specialization</label>
                                    <input type="text" value={updatedSpecialization} onChange={e => setUpdatedSpecialization(e.target.value)} className="form-control" placeholder="Specialization"/>
                                </div>

                                <div className="form-group">
                                    <label>Technology</label>
                                    <input type="text" value={updatedTechnology} onChange={e => setUpdatedTechnology(e.target.value)} className="form-control" placeholder="Technology"/>
                                </div>

                            </>
                        }
                    </form>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={handleSubmit}>Update</Button> {' '}
                    <div><Button onClick={() => setShowUpdateUserModal(false)}>Close</Button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    )

}