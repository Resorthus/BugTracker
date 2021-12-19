import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"
import DatePicker from "react-datepicker";

export default function CreateProject({setProjects, setShowProjectCreateModal, showProjectCreateModal})
{
    const [isLoaded, setIsLoaded] = useState(false);
    const {token, seToken} = useToken();
    const [newProject, setNewProject] = useState(null);

    const [name, setName] = useState();
    const [deadline, setDeadline] = useState();
    const [programmerCount, setProgrammerCount] = useState();

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                Name: name,
                Deadline: deadline,
                ProgrammerCount: programmerCount
            })
        };

        fetch("http://localhost:8000/api/projects/", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setNewProject(data);
                    setIsLoaded(true);
                }
            )
    }

    useEffect(() => {
        if(isLoaded) {
            setProjects(projects => [...projects, newProject]);
            setShowProjectCreateModal(false);
        }
    }, [isLoaded])

    return (
        <>
            <Modal show={showProjectCreateModal}>
                <Modal.Header>New Project</Modal.Header>
                <Modal.Body>
                    <form onSubmit={handleSubmit}>

                        <div className="form-group">
                            <label>Name</label>
                            <input type="text" onChange={e => setName(e.target.value)} className="form-control" placeholder="Name"/>
                        </div>
                        <div className="form-group">
                            <label>Deadline</label><br/>
                            <DatePicker
                                className="form-control"
                                placeholder="Deadline"
                                selected={deadline}
                                onChange={(date) => setDeadline(date)}
                                showTimeSelect
                                timeFormat="HH:mm"
                                timeIntervals={15}
                                timeCaption="time"
                                dateFormat="yyyy, MMMM d h:mm aa"
                            />
                        </div>
                        <div className="form-group">
                            <label>Programmer Count</label>
                            <input type="text" onChange={e => setProgrammerCount(e.target.value)} className="form-control" placeholder="Programmer Count"/>
                        </div>
                    </form>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={handleSubmit}>Create</Button> {' '}
                    <div><Button onClick={() => setShowProjectCreateModal(false)}>Close</Button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    )

}