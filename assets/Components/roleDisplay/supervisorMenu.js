import {Link} from "react-router-dom";
import React from "react";
import logout from "../services/logout";

export default function SupervisorMenu({onLogOut}) {

    return (
        <nav className="navbar navbar-expand navbar-dark text-white" style={{marginBottom: '50px',backgroundColor: '#348C31'}}>
            <div className="collapse navbar-collapse" id="navbarText">
                <ul className="navbar-nav mr-auto">
                    <li className="nav-item">
                        <Link className={"nav-link"} to={"/"}>Home</Link>
                    </li>
                    <li className="nav-item">
                        <Link className={"nav-link"} to={"/supervisor/projects"}>  Manage Projects </Link>
                    </li>
                    <li className="nav-item">
                        <Link className={"nav-link"} onClick={() => logout()} to={'/'}> Logout </Link>
                    </li>
                </ul>
            </div>
        </nav>
    );
}