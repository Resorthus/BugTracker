import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"

export default function UpdateBug({setBugs, setShowBugUpdateModal, showBugUpdateModal, project, user, bugs, bug, setBug, index})
{
    const [isLoaded, setIsLoaded] = useState(false);
    const {token, seToken} = useToken();

    const [updatedDescription, setUpdatedDescription] = useState(bug.Description);
    const [updatedSeverity, setUpdatedSeverity] = useState(bug.Severity);
    const [updatedStatus, setUpdatedStatus] = useState(bug.Status);
    const [updatedResponsibility_id, setUpdatedResponsibility_id] = useState();
    const [programmers, setProgrammers] = useState();

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                description: updatedDescription,
                severity: updatedSeverity,
                status: updatedStatus,
                responsibility_id : updatedResponsibility_id
            })
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

        const requestOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            }
        };

        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setProgrammers(data);
                    setUpdatedResponsibility_id(data[0].id);
                }
            )

    }, [])

    useEffect(() => {
        if(isLoaded) {
            let bugsCopy = bugs;
            bugsCopy[index] = bug;
            setBugs(bugsCopy);
            setShowBugUpdateModal(false);
        }
    }, [isLoaded])

    return (
        <>
            { programmers &&
            <Modal show={showBugUpdateModal}>
                <Modal.Header>New Bug</Modal.Header>
                <Modal.Body>
                    <form onSubmit={handleSubmit}>

                        <div className="form-group">
                            <label>Description</label>
                            <input type="text" value={updatedDescription} onChange={e => setUpdatedDescription(e.target.value)}
                                   className="form-control" placeholder="Description"/>
                        </div>
                        <div className="form-group">
                            <label>Severity</label><br/>
                            <select className="form-control" value={updatedSeverity} onChange={e => setUpdatedSeverity(e.target.value)}>
                                <option selected="selected" value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div className="form-group">
                            <label>Responsible Programmer</label><br/>
                            <select className="form-control" value={updatedResponsibility_id} onChange={e => setUpdatedResponsibility_id(e.target.value)}>
                                {programmers.map((programmer) => (
                                    <option
                                        value={programmer.id}>{programmer.FirstName + " " + programmer.LastName}</option>
                                ))}
                            </select>
                        </div>
                        <div className="form-group">
                            <label>Status</label><br/>
                            <select className="form-control" value={updatedStatus} onChange={e => setUpdatedStatus(e.target.value)}>
                                <option value="Active">Active</option>
                                <option value="Fixed">Finished</option>
                            </select>
                        </div>
                    </form>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={handleSubmit}>Update</Button> {' '}
                    <div><Button onClick={() => setShowBugUpdateModal(false)}>Close</Button>
                    </div>
                </Modal.Footer>
            </Modal>
            }
        </>
    )

}