import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import Confirm from './markAsFinished';
import back from "../../styles/back.svg";
import view from "../../styles/view.svg";
import waiting from "../../styles/waiting.svg";
import done from "../../styles/done.svg";


export default function getAll({user, project}){
    const {token, setToken} = useToken();
    const [bugs, setBugs] = useState();

    const [showBugModal, setShowBugModal] = useState(false);
    const [showBugConfirmModal, setShowBugConfirmModal] = useState(false);
    const [index, setIndex] = useState(false);

    const [bug, setBug] = useState(null);

    const handleShowBugModal = (bug) => {
        setShowBugModal(true);
        setBug(bug);
    }

    const handleCloseModal = () => {
        setShowBugModal(false);
    }

    const handleConfirmBugModal = (bug, i) => {
        setShowBugConfirmModal(true);
        setBug(bug);
        setIndex(i);
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

        console.log(project);
        console.log(user);
        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers/" + user.id + "/bugs", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setBugs(data.ResponsibleForBugs);
                }
            )

    }, [])


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                <Button style={{backgroundColor: '#e0e0d1'}}><img style={{width: '20px', height: '20px'}} src={back} onClick={() => window.location.reload(false)}/></Button>
                { bugs ?
                    <table className="table">
                        <thead className="thead-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Description</th>
                            <th scope="col">Severity</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        {bugs.map((bug, i) => (
                            <tr>
                                <th scope="row">{bug.id}</th>
                                <td>{bug.Description}</td>
                                <td>{bug.Severity}</td>
                                <td>{bug.Status == "Active" ?
                                    <img style={{width: '20px', height: '20px'}} src={waiting}/>:
                                    <img style={{width: '20px', height: '20px'}} src={done}/>
                                }</td>
                                <td>
                                    <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={view}  onClick={() => handleShowBugModal(bug)} /></Button> {' '}
                                    {
                                        bug.Status == "Active" &&
                                        <Button style={{backgroundColor: '#f2f288'}}> <img style={{width: '20px', height: '20px'}} src={done} onClick={() => handleConfirmBugModal(bug, i)} /></Button>
                                    }
                                </td>
                            </tr>
                        ))}
                        </tbody>
                    </table> :
                    null
                }

                {
                    showBugModal &&
                    <Modal show={showBugModal}>
                        <Modal.Header>Bug Info</Modal.Header>
                        <Modal.Body>
                            <div className="row">
                                <div className="col-sm"><b>Id:</b></div>
                                <div className="col-sm">{bug.id}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Description:</b></div>
                                <div className="col-sm">{bug.Description}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Severity:</b></div>
                                <div className="col-sm">{bug.Severity}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Status:</b></div>
                                <div className="col-sm">{bug.Status}</div>
                                <div className="w-100"/>
                                <div className="col-sm"><b>Submission Date:</b></div>
                                <div className="col-sm">{getFormattedDate(bug.Date)}</div>
                            </div>
                        </Modal.Body>
                        <Modal.Footer>
                            <div><Button onClick={handleCloseModal}>Close</Button>
                            </div>
                        </Modal.Footer>
                    </Modal>
                }

                {
                    showBugConfirmModal &&
                        <Confirm project={project}
                                 bug={bug}
                                 setBugs={setBugs}
                                 bugs={bugs}
                                 setBug={setBug}
                                 user={user}
                                 showConfirmBugModal={showBugConfirmModal}
                                 setShowConfirmBugModal={setShowBugConfirmModal}
                                 index={index}
                        />
                }

            </div>
        </>
    )
}