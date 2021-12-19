import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";

export default function MarkAsFinished({bug, setBug, setBugs, index, bugs, showConfirmBugModal, setShowConfirmBugModal, project, user})
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

        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers/" + user.id + "/bugs/" + bug.id, requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setBug(data);
                    setIsLoaded(true);

                }
            )
    }

    useEffect(() => {
        if (isLoaded)
        {
            let bugsCopy = bugs;
            bugsCopy[index] = bug;
            setBugs(bugsCopy);
            setShowConfirmBugModal(false);
        }

    }, [isLoaded])

    return(
        <Modal show={showConfirmBugModal}>
            <Modal.Header>Confirmation</Modal.Header>
            <Modal.Body>Are you sure you want to mark this bug as finished?</Modal.Body>
            <Modal.Footer>
                <Button onClick={() => setShowConfirmBugModal(false)}>No</Button>
                <Button onClick={handleSubmit}>Yes</Button>
            </Modal.Footer>
        </Modal>
    )

}