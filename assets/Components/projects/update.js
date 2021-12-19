import React, {useState, useEffect} from 'react';
import useToken from "../services/token";
import Modal from "react-bootstrap/Modal";
import {Button} from "react-bootstrap"
import DatePicker from "react-datepicker";

export default function UpdateUser({project, showUpdateProjectModal, setShowUpdateProjectModal, setProject, setProjects, index, projects})
{
    const [isLoaded, setIsLoaded] = useState(false);
    const {token, seToken} = useToken();

    const [updatedName, setUpdatedName] = useState(project.Name);
    const [updatedDeadline, setUpdatedDeadline] = useState(new Date(project.Deadline));
    const [updatedProgrammerCount, setUpdatedProgrammerCount] = useState(project.ProgrammerCount);

    const handleSubmit = e => {
        e.preventDefault();

        const requestOptions = {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                Name: updatedName,
                Deadline: updatedDeadline,
                ProgrammerCount: updatedProgrammerCount
            })
        };

        fetch("http://localhost:8000/api/projects/" + project.id, requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    setProject(data);
                    setIsLoaded(true);
                }
            )
    }

    useEffect(() => {
        if(isLoaded) {
            let projectsCopy = projects;
            projectsCopy[index] = project;
            setProjects(projectsCopy);
            setShowUpdateProjectModal(false);
        }
    }, [isLoaded])

    return (
        <>
            <Modal show={showUpdateProjectModal}>
                <Modal.Header>Update project info</Modal.Header>
                <Modal.Body>
                    <form onSubmit={handleSubmit}>

                        <div className="form-group">
                            <label>Name</label>
                            <input type="text" value={updatedName} onChange={e => setUpdatedName(e.target.value)} className="form-control" placeholder="Name"/>
                        </div>
                        <div className="form-group">
                            <label>Deadline</label><br/>
                            <DatePicker
                                className="form-control"
                                placeholder="Deadline"
                                selected={updatedDeadline}
                                onChange={(date) => setUpdatedDeadline(date)}
                                showTimeSelect
                                timeFormat="HH:mm"
                                timeIntervals={15}
                                timeCaption="time"
                                dateFormat="yyyy, MMMM d h:mm aa"
                            />
                        </div>
                        <div className="form-group">
                            <label>Programmer Count</label>
                            <input type="text" value={updatedProgrammerCount} onChange={e => setUpdatedProgrammerCount(e.target.value)} className="form-control" placeholder="Programmer Count"/>
                        </div>
                    </form>
                </Modal.Body>
                <Modal.Footer>
                    <Button onClick={handleSubmit}>Update</Button> {' '}
                    <div><Button onClick={() => setShowUpdateProjectModal(false)}>Close</Button>
                    </div>
                </Modal.Footer>
            </Modal>
        </>
    )
}