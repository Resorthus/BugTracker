import useToken from "../services/token";
import React, {useEffect, useState} from "react";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";

export default function DeleteProject({userToDeleteId, setUserToDeleteId, showDeleteUserModal, setShowDeleteUserModal, users, setUsers})
{
    const {token, seToken} = useToken();
    const [isLoaded, setIsLoaded] = useState(false);

    const handleSubmit = e => {
        e.preventDefault();
        const requestOptions = {
            method: 'DELETE',
            headers: {
                'Authorization': "Bearer " + token
            }
        };
        fetch('http://localhost:8000/api/users/' + userToDeleteId, requestOptions)
            .then(() => {
                setIsLoaded(true);
            })
    }

    useEffect(() => {
        if(isLoaded) {
            let usersCopy = users;

            let index = usersCopy.findIndex(u => u.id == userToDeleteId);

            usersCopy.splice(index, 1);

            setUsers(usersCopy);
            setUserToDeleteId = null;
            setShowDeleteUserModal(false);
        }
    }, [isLoaded])

    return (
        <Modal show={showDeleteUserModal}>
            <Modal.Header>Confirmation</Modal.Header>
            <Modal.Body>Are you sure you want to delete this user?</Modal.Body>
            <Modal.Footer>
                <Button onClick={() => setShowDeleteUserModal(false)}>No</Button>
                <Button onClick={handleSubmit}>Yes</Button>
            </Modal.Footer>
        </Modal>
    )
}