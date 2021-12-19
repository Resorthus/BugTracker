import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import Update from './update';
import Delete from './delete';
import Confirm from './confirm';
import view from "../../styles/view.svg";
import edit from "../../styles/edit.svg";
import remove from "../../styles/remove.svg";
import done from "../../styles/done.svg";
import confirm from "../../styles/confirm.svg";


export default function getAll(){
    const {token, setToken} = useToken();
    const [response, setResponse] = useState();
    const [errors, setErrors] = useState();
    const [users, setUsers] = useState();

    const [showUserModal, setShowUserModal] = useState(false);
    const [user, setUser] = useState(null);
    const [index, setIndex] = useState();

    const [userToDeleteId, setUserToDeleteId] = useState(null);
    const [showUserUpdateModal, setShowUserUpdateModal] = useState();
    const [showDeleteUserModal, setShowDeleteUserModal] = useState();
    const [showConfirmUserModal, setConfirmUserModal] = useState();

    const handleDeleteUser = (id) => {
        setUserToDeleteId(id);
        setShowDeleteUserModal(true);
    }

    const handleCloseModal = () => {
        setShowUserModal(false);
    }

    const handleShowUpdateModal = (user, i) => {
        setUser(user);
        setIndex(i);
        setShowUserUpdateModal(true);
    }


    const handleShowProjectModal = (user) => {
        setShowUserModal(true);
        setUser(user);
    }

    const handleConfirmation = (user, i) => {
        setUser(user);
        setIndex(i);
        setConfirmUserModal(true);
    }

    const getFormattedDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString();
    }

    useEffect(() => {
        const requestOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            }
        }

        fetch("http://localhost:8000/api/users/", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setUsers(data);
                },
                (error) => {
                    setErrors(error);
                }
            )

    }, [])


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>

                { users ?
                    <table className="table">
                        <thead className="thead-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">First Name</th>
                            <th scope="col">Last Name</th>
                            <th scope="col">Role</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        {users.map((user, i) => (
                            <tr>
                                <th scope="row">{user.id}</th>
                                <td>{user.FirstName}</td>
                                <td>{user.LastName}</td>
                                <td>{user.roles[0].substring(5)}</td>
                                <td>
                                    {
                                        user.isConfirmed == true &&
                                        <img style={{width: '50px', height: '20px'}} src={done
                                        }/>

                                    }
                                    {
                                        user.isConfirmed == false &&
                                        <td>
                                            <Button style={{backgroundColor: '#ffff00'}}><img style={{width: '20px', height: '20px'}} src={confirm}  onClick={() => handleConfirmation(user, i)}/></Button>
                                        </td>
                                    }
                                </td>
                                <td>
                                    <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={view}  onClick={() => handleShowProjectModal(user)} /></Button> {' '}
                                    <Button style={{backgroundColor: '#4CAF50'}}><img style={{width: '20px', height: '20px'}} src={edit}  onClick={() => handleShowUpdateModal(user, i)} /></Button> {' '}
                                    <Button style={{backgroundColor: '#f44336'}}><img style={{width: '20px', height: '20px'}} src={remove}  onClick={() => handleDeleteUser(user.id)}/></Button>
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table> :
                    null
                }

                {
                    showUserModal &&
                    <Modal show={showUserModal}>
                        <Modal.Header>User Info</Modal.Header>
                        <Modal.Body>
                            <div className="row">
                                <div className="col-sm"><b>Id:</b></div>
                                <div className="col-sm">{user.id}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Role:</b></div>
                                <div className="col-sm">{user.roles[0].substring(5)}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>First Name:</b></div>
                                <div className="col-sm">{user.FirstName}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Last Name:</b></div>
                                <div className="col-sm">{user.LastName}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Birthdate:</b></div>
                                <div className="col-sm">{getFormattedDate(user.Birthdate)}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Email:</b></div>
                                <div className="col-sm">{user.email}</div>
                                <div className="w-100"/>
                                {
                                    user.roles[0] == "ROLE_PROGRAMMER" &&
                                        <>
                                    <div className="col-sm"><b>Specialization:</b></div>
                                    <div className="col-sm">{user.Specialization}</div>
                                    <div className="w-100"/>
                                    <div className="col-sm"><b>Level:</b></div>
                                    <div className="col-sm">{user.Level}</div>
                                    <div className="w-100"/>
                                    <div className="col-sm"><b>Technology:</b></div>
                                    <div className="col-sm">{user.Technology}</div>
                                    <div className="w-100"/>
                                    </>
                                }
                            </div>
                        </Modal.Body>
                        <Modal.Footer>
                            <div><Button onClick={handleCloseModal}>Close</Button>
                            </div>
                        </Modal.Footer>
                    </Modal>
                }

                {
                    showUserUpdateModal &&
                        <Update user={user}
                                setUser={setUser}
                                showUpdateUserModal={showUserUpdateModal}
                                setShowUpdateUserModal={setShowUserUpdateModal}
                                index={index}
                                users={users}
                                setUsers={setUsers}
                        />
                }

                {
                    showDeleteUserModal &&
                    <Delete userToDeleteId={userToDeleteId}
                            setUserToDeleteId={setUserToDeleteId}
                            showDeleteUserModal={showDeleteUserModal}
                            setShowDeleteUserModal={setShowDeleteUserModal}
                            users={users}
                            setUsers={setUsers}
                    />
                }

                {
                    showConfirmUserModal &&
                        <Confirm user={user}
                                 setUser={setUser}
                                 users={users}
                                 setUsers={setUsers}
                                 index={index}
                                 showConfirmUserModal={showConfirmUserModal}
                                 setShowConfirmUserModal={setConfirmUserModal}
                        />
                }

            </div>
        </>
    )
}