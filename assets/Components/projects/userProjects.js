import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import Create from "./create";
import Update from "./update";
import Delete from "./delete";
import SubmittedBugs from '../bugs/getSubmittedBugs';
import BugsToFix  from '../bugs/getBugsToFix';

export default function getAll(){
    const {token, setToken} = useToken();

    const [projects, setProjects] = useState();
    const [project, setProject] = useState();
    const [error, setError] = useState(null);
    const [user, setUser] = useState();
    const [showSubmittedBugs, setShowSubmittedBugs] = useState(false);
    const [showBugsToFix, setShowBugsToFix] = useState(false);
    const [showProjects, setShowProjects] = useState(true);


    useEffect(() => {
        const requestOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            }
        }

        let userEmail = JSON.parse(atob(token.split(".")[1])).username;

        fetch("http://localhost:8000/api/users/" + userEmail, requestOptions)
            .then(res => {
                return res.json();
            })
            .then(
                (data) => {
                    setUser(data);
                    fetch("http://localhost:8000/api/users/" + data.id + "/projects", requestOptions)
                        .then(r => {
                            if (!r.ok) setError("You must wait until administrator has confirmed your registration");
                          return  r.json()
                        })
                        .then(
                            (proj) => {
                                setProjects(proj);
                            }
                        )
                }
            )
    }, [])

    const handleShowSubmittedBugs = (project, user) => {
        setProject(project);
        setUser(user);
        setShowSubmittedBugs(true);
        setShowProjects(false);


    }

    const handleShowBugsToFix = (project, user) => {
        setProject(project);
        setUser(user);
        setShowBugsToFix(true);
        setShowProjects(false);
    }

    const getFormattedDate = (dateStr) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString();
    }


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                {error ? <div className="alert alert-danger" role="alert">
                        <b>{error}</b>
                    </div> :
                    showProjects && projects && user ?
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
                                        <Button style={{marginBottom: '2px', backgroundColor:'#036323'}}
                                                onClick={() => handleShowSubmittedBugs(project, user)}>View Submitted
                                            Bugs</Button> {' '}
                                        <Button style={{marginBottom: '2px', backgroundColor:'#4f3302'}}
                                                onClick={() => handleShowBugsToFix(project, user)}>View Bugs to
                                            Fix</Button>{' '}
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table> :
                        null
                }


                {
                    showSubmittedBugs &&
                        <SubmittedBugs project={project}
                                       user={user}
                        />
                }

                {
                    showBugsToFix &&
                        <BugsToFix project={project}
                                   user={user}
                        />
                }
            </div>
        </>
    )
}