import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import back from "../../styles/back.svg";
import view from "../../styles/view.svg";
import done from "../../styles/done.svg";
import waiting from "../../styles/waiting.svg";


export default function getAll({project}){
    const {token, setToken} = useToken();
    const [bugs, setBugs] = useState();

    const [showBugModal, setShowBugModal] = useState(false);

    const [bug, setBug] = useState(null);

    const handleShowBugModal = (bug) => {
        setShowBugModal(true);
        setBug(bug);
    }

    const handleCloseModal = () => {
        setShowBugModal(false);
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

        fetch("http://localhost:8000/api/projects/" + project.id + "/bugs", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setBugs(data);
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
                                    <Button style={{backgroundColor: '#9999ff'}}><img style={{width: '20px', height: '20px'}} src={view}  onClick={() => handleShowBugModal(bug)} /></Button>
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

            </div>
        </>
    )
}