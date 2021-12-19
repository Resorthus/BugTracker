import useToken from "../services/token";
import React, {useEffect, useState} from "react";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";

export default function DeleteProject({bugToDeleteId, setBugToDeleteId, showDeleteBugModal, setShowDeleteBugModal, bugs, setBugs, project, user})
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
        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers/" + user.id + "/bugs/" + bugToDeleteId, requestOptions)
            .then(() => {
                setIsLoaded(true);
            })
    }

    useEffect(() => {
        if(isLoaded) {
            let bugsCopy = bugs;

            let index = bugsCopy.findIndex(b => b.id == bugToDeleteId);

            bugsCopy.splice(index, 1);

            setBugs(bugsCopy);
            setBugToDeleteId = null;
            setShowDeleteBugModal(false);
        }
    }, [isLoaded])

    return (
        <Modal show={showDeleteBugModal}>
            <Modal.Header>Confirmation</Modal.Header>
            <Modal.Body>Are you sure you want to delete this bug?</Modal.Body>
            <Modal.Footer>
                <Button onClick={() => setShowDeleteBugModal(false)}>No</Button>
                <Button onClick={handleSubmit}>Yes</Button>
            </Modal.Footer>
        </Modal>
    )
}