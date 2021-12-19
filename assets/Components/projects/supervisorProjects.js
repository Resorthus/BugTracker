import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import ProjectProgrammers from './projectProgrammers';
import ProjectBugs from './projectBugs';
import view from "../../styles/view.svg";
import user from "../../styles/user.svg";
import bug from "../../styles/bug.svg";
import edit from "../../styles/edit.svg";

export default function getAll(){
    const {token, setToken} = useToken();
    const [errors, setErrors] = useState(null);
    const [projects, setProjects] = useState();
    const [response, setResponse] = useState();

    const [showProjectModal, setShowProjectModal] = useState(false);
    const [project, setProject] = useState(null);
    const [index, setIndex] = useState();

    const [showProjects, setShowProjects] = useState(true);
    const [showProjectProgrammers, setShowProjectProgrammers] = useState(false);
    const [showProjectBugs, setShowProjectBugs] = useState(false);


    const handleShowProjectProgrammers = (project) =>{
        setProject(project);
        setShowProjects(false);
        setShowProjectProgrammers(true);
    }

    const handleShowProjectBugs = (project) =>{
        setProject(project);
        setShowProjects(false);
        setShowProjectBugs(true);
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
            .then(res => {
                if(!res.ok) setErrors("You must wait until administrator has confirmed your registration");
                return res.json();
            })
            .then(
                (data) => {
                    setProjects(data);
                }
            )

    }, [])


    return (

            <div className="container-sm mt-1 border rounded" style={{padding: '5px', marginBottom: "50px", background: 'white'}}>
                { errors ?                     <div className="alert alert-danger" role="alert">
                        <b>{errors}</b>
                    </div> :
                    projects && showProjects ?
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
                            {projects.map((project) => (
                                <tr>
                                    <th scope="row">{project.id}</th>
                                    <td>{project.Name}</td>
                                    <td>{getFormattedDate(project.Deadline)}</td>
                                    <td style={{textAlign: "center"}}>{project.ProgrammerCount}</td>
                                    <td>
                                        <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={user}  onClick={() => handleShowProjectProgrammers(project)} /></Button> {' '}
                                        <Button style={{backgroundColor: '#4CAF50'}}><img style={{width: '20px', height: '20px'}} src={bug}  onClick={() => handleShowProjectBugs(project)} /></Button> {' '}
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table> :
                        null

                }

                {
                    showProjectProgrammers &&
                        <ProjectProgrammers project={project}/>
                }

                {
                    showProjectBugs &&
                        <ProjectBugs project={project}/>
                }


            </div>

    )
}