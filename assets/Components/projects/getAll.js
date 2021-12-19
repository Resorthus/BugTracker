import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import Create from "./create";
import Update from "./update";
import Delete from "./delete";
import view from "../../styles/view.svg";
import edit from "../../styles/edit.svg";
import remove from "../../styles/remove.svg";
import add from "../../styles/add.svg";

export default function getAll(){
    const {token, setToken} = useToken();
    const [response, setResponse] = useState();
    const [errors, setErrors] = useState();
    const [projects, setProjects] = useState();

    const [showProjectModal, setShowProjectModal] = useState(false);
    const [project, setProject] = useState(null);
    const [index, setIndex] = useState();

    const [projectToDeleteId, setProjectToDeleteId] = useState(null);
    const [showProjectUpdateModal, setShowProjectUpdateModal] = useState();
    const [showProjectCreateModal, setShowProjectCreateModal] = useState();
    const [showDeleteProjectModal, setShowDeleteProjectModal] = useState();

    const handleDeleteProject = (id) => {
        setProjectToDeleteId(id);
        setShowDeleteProjectModal(true);
    }

    const handleCloseModal = () => {
        setShowProjectModal(false);
    }

    const handleShowUpdateModal = (project, i) => {
        setProject(project);
        setIndex(i);
        setShowProjectUpdateModal(true);
    }

    const handleShowCreateModal = () => {
        setShowProjectCreateModal(true);
    }

    const handleShowProjectModal = (project) => {
        setShowProjectModal(true);
        setProject(project);
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

        fetch("http://localhost:8000/api/projects/", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setProjects(data);
                },
                (error) => {
                    setErrors(error);
                }
            )

    }, [])


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                {projects &&
                <Button style={{backgroundColor: '#e0e0d1'}}><img src={add} style={{width: '20px', height: '20px'}} onClick={handleShowCreateModal}/></Button>}

            { projects ?
                <table className="table">
                    <thead className="thead-dark">
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Deadline</th>
                        <th scope="col">Suggested Programmer Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    {projects.map((project, i) => (
                        <tr>
                            <th scope="row">{project.id}</th>
                            <td>{project.Name}</td>
                            <td>{getFormattedDate(project.Deadline)}</td>
                            <td style={{textAlign: "center"}}>{project.ProgrammerCount}</td>
                            <td>
                                <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={view}  onClick={() => handleShowProjectModal(project)} /></Button> {' '}
                                <Button style={{backgroundColor: '#4CAF50'}}><img style={{width: '20px', height: '20px'}} src={edit}  onClick={() => handleShowUpdateModal(project, i)} /></Button> {' '}
                                <Button style={{backgroundColor: '#f44336'}}><img style={{width: '20px', height: '20px'}} src={remove}  onClick={() => handleDeleteProject(project.id)}/></Button>
                            </td>
                        </tr>
                    ))}
                    </tbody>
                </table> :
                null
            }

            {
                showProjectModal &&
                <Modal show={showProjectModal}>
                    <Modal.Header>Project info</Modal.Header>
                    <Modal.Body>
                        <div className="row">
                            <div className="col-sm"><b>Id:</b></div>
                            <div className="col-sm">{project.id}</div>
                            <div className="w-100"/>
                            <div className="col-sm"><b>Name:</b></div>
                            <div className="col-sm">{project.Name}</div>
                            <div className="w-100"/>
                            <div className="col-sm"><b>Deadline:</b></div>
                            <div className="col-sm">{getFormattedDate(project.Deadline)}</div>
                            <div className="w-100"/>
                            <div className="col-sm"><b>Suggested Programmer Count:</b></div>
                            <div className="col-sm">{project.ProgrammerCount}</div>
                        </div>
                    </Modal.Body>
                    <Modal.Footer>
                        <div><Button onClick={handleCloseModal}>Close</Button>
                        </div>
                    </Modal.Footer>
                </Modal>
            }

            {
                showProjectCreateModal &&
                    <Create setProjects={setProjects}
                            setShowProjectCreateModal={setShowProjectCreateModal}
                            showProjectCreateModal={showProjectCreateModal}
                    />
            }

            {
                showProjectUpdateModal &&
                    <Update project={project}
                            setShowUpdateProjectModal={setShowProjectUpdateModal}
                            showUpdateProjectModal={showProjectUpdateModal}
                            setProject={setProject}
                            setProjects={setProjects}
                            index={index}
                            projects={projects}
                    />
            }

            {
                showDeleteProjectModal &&
                    <Delete projectToDeleteId={projectToDeleteId}
                            setProjectToDeleteId={projectToDeleteId}
                            showDeleteProjectModal={showDeleteProjectModal}
                            setShowDeleteProjectModal={setShowDeleteProjectModal}
                            projects={projects}
                            setProjects={setProjects}
                    />
            }

            </div>
        </>
    )
}