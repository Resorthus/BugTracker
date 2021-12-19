import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import user from "../../styles/user.svg";
import back from "../../styles/back.svg";
import view from "../../styles/view.svg";


export default function getAll({project}){
    const {token, setToken} = useToken();
    const [users, setUsers] = useState();

    const [showUserModal, setShowUserModal] = useState(false);
    const [user, setUser] = useState(null);

  const  handleShowUserModal = (user) => {
      setUser(user);
      setShowUserModal(true);
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
        };

        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setUsers(data);
                }
            )

    }, [])


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                <Button style={{backgroundColor: '#e0e0d1'}}><img style={{width: '20px', height: '20px'}} src={back} onClick={() => window.location.reload(false)}/></Button>
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
                                    <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={view}  onClick={() => handleShowUserModal(user)} /></Button>
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
                                <div className="col-sm">{user.roles[0]}</div>
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
                            <div><Button onClick={event => setShowUserModal(false)}>Close</Button>
                            </div>
                        </Modal.Footer>
                    </Modal>
                }

            </div>
        </>
    )
}