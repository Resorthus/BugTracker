import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";

export default function ConfirmUser({user, setUser, setUsers, index, users, showConfirmUserModal, setShowConfirmUserModal})
{
    const {token, seToken} = useToken();
    const [isLoaded, setIsLoaded] = useState(false);

    const handleSubmit = e => {
        e.preventDefault();
        const requestOptions = {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
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

    useEffect(() => {
    if (isLoaded)
    {
        let usersCopy = users;
        usersCopy[index] = user;
        setUsers(usersCopy);
        setShowConfirmUserModal(false);
    }

    }, [isLoaded])

    return(
        <Modal show={showConfirmUserModal}>
            <Modal.Header>Confirmation</Modal.Header>
            <Modal.Body>Are you sure you want to confirm this user?</Modal.Body>
            <Modal.Footer>
                <Button onClick={() => setShowConfirmUserModal(false)}>No</Button>
                <Button onClick={handleSubmit}>Yes</Button>
            </Modal.Footer>
        </Modal>
    )

}