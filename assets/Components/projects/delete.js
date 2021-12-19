import useToken from "../services/token";
import React, {useEffect, useState} from "react";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap";

export default function DeleteProject({projectToDeleteId, setProjectToDeleteId, showDeleteProjectModal, setShowDeleteProjectModal, projects, setProjects})
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
        fetch('http://localhost:8000/api/projects/' + projectToDeleteId, requestOptions)
            .then(res => {
                setIsLoaded(true);
            })
    }

    useEffect(() => {
        if(isLoaded) {
            let projectsCopy = projects;

            let index = projectsCopy.findIndex(p => p.id == projectToDeleteId);

            projectsCopy.splice(index, 1);

            setProjects(projectsCopy);
            setProjectToDeleteId = null;
            setShowDeleteProjectModal(false);
        }
    }, [isLoaded])

    return (
        <Modal show={showDeleteProjectModal}>
            <Modal.Header>Confirmation</Modal.Header>
            <Modal.Body>Are you sure you want to delete this project?</Modal.Body>
            <Modal.Footer>
                <Button onClick={() => setShowDeleteProjectModal(false)}>No</Button>
                <Button onClick={handleSubmit}>Yes</Button>
            </Modal.Footer>
        </Modal>
    )
}