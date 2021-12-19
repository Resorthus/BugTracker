import React, {useEffect, useState} from 'react';
import useToken from "../services/token";
import {Button} from "react-bootstrap";
import Modal from "react-bootstrap/Modal";
import Create from "./create";
import Update from "./update";
import Delete from "./delete";

export default function ManageParticipations(){
    const {token, setToken} = useToken();
    const [success, setSuccess] = useState(null)

    const [projects, setProjects] = useState();
    const [users, setUsers] = useState();
    const [action, setAction] = useState();
    const [projectId, setProjectId] = useState();
    const [userId, setUserId] = useState();

    const handleSubmit = e => {
        e.preventDefault();
        const requestOptions = {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': "Bearer " + token
            },
            body: JSON.stringify({
                action:action
            })
        };
        fetch('http://localhost:8000/api/projects/' + projectId + '/programmers/' + userId, requestOptions)
            .then(res =>  {
                setSuccess("Programmer participation was modified successfully!");
            })
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
                    setProjectId(data[0].id)
                }
            )

        fetch("http://localhost:8000/api/users/", requestOptions)
            .then(res => res.json())
            .then(
                (data) => {
                    let programmers = data.filter(u => u.roles[0] == "ROLE_PROGRAMMER");
                    setUsers(programmers);
                    setUserId(programmers[0].id)
                }
            )

    }, [])


    return (
        <>
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                {success &&
                    <div className="alert alert-success" role="alert">
                        <b>{success}</b>
                    </div>
                }
                { projects && users ?
                    <>
                    <div className="form-group">
                        <label><b>Project</b></label>
                        <select className="form-control" onChange={e => setProjectId(e.target.value)}>

                            {projects.map((project) => (
                                <option value={project.id}>{project.Name}</option>
                            ))}

                        </select>
                    </div>


                    <div className="form-group">
                        <label><b>Programmer</b></label>
                        <select className="form-control" onChange={e => setUserId(e.target.value)}>

                            {users.map((user) => (
                                <option value={user.id}>{user.FirstName + " " + user.LastName}</option>
                            ))}

                        </select>
                    </div>
                        < br ></br>
                        <label><b>Select action</b></label>

                        <div onChange={e => setAction(e.target.value)}>
                            <input type="radio" value="add" name="action" /> Add
                            <br></br>
                            <input type="radio" value="delete" name="action" /> Remove
                        </div>

                        <br></br>

                        <Button onClick={handleSubmit}>Set</Button>
                        </>:
                    null

                }

            </div>
        </>
    )
}