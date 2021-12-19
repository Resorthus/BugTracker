import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"

export default function CreateBug({setBugs, setShowBugCreateModal, showBugCreateModal, project, user})
{
    const [isLoaded, setIsLoaded] = useState(false);
    const {token, seToken} = useToken();

    const [description, setDescription] = useState();
    const [severity, setSeverity] = useState("Low");
    const [status, setStatus] = useState("Active");
    const [responsibility_id, setResponsibility_id] = useState();
    const [programmers, setProgrammers] = useState();
    const [bug, setNewBug] = useState();

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                description: description,
                severity: severity,
                status: status,
                responsibility_id : responsibility_id
            })
        };


        fetch("http://localhost:8000/api/projects/" + project.id + "/programmers/" + user.id + "/bugs", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setNewBug(data);
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
                    setResponsibility_id(data[0].id);
                }
            )

    }, [])

    useEffect(() => {
        if(isLoaded) {
            setBugs(projects => [...projects, bug]);
            setShowBugCreateModal(false);
        }
    }, [isLoaded])

    return (
        <>
            { programmers &&
                <Modal show={showBugCreateModal}>
                    <Modal.Header>New Bug</Modal.Header>
                    <Modal.Body>
                        <form onSubmit={handleSubmit}>

                            <div className="form-group">
                                <label>Description</label>
                                <input type="text" onChange={e => setDescription(e.target.value)}
                                       className="form-control" placeholder="Description"/>
                            </div>
                            <div className="form-group">
                                <label>Severity</label><br/>
                                <select className="form-control" onChange={e => setSeverity(e.target.value)}>
                                    <option selected="selected" value="Low">Low</option>
                                    <option value="Medium">Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                            <div className="form-group">
                                <label>Responsible Programmer</label><br/>
                                <select className="form-control" onChange={e => setResponsibility_id(e.target.value)}>
                                    {programmers.map((programmer) => (
                                        <option
                                            value={programmer.id}>{programmer.FirstName + " " + programmer.LastName}</option>
                                    ))}
                                </select>
                            </div>
                        </form>
                    </Modal.Body>
                    <Modal.Footer>
                        <Button onClick={handleSubmit}>Create</Button> {' '}
                        <div><Button onClick={() => setShowBugCreateModal(false)}>Close</Button>
                        </div>
                    </Modal.Footer>
                </Modal>
            }
        </>
    )

}