import React from 'react';
import {Route, Routes} from 'react-router-dom';
import Test from './startPage';
import Projects from './projects/getAll'
import Users from './users/getAll';
import Participation from './projects/participation';
import UserProjects from './projects/userProjects';
import SupervisorProjects from './projects/supervisorProjects';
import "../styles/styles.css";
import AdminMenu from './roleDisplay/adminMenu';
import ProgrammerMenu from './roleDisplay/programmerMenu';
import SupervisorMenu from './roleDisplay/supervisorMenu';


import Login from './services/login';
import useToken from "./services/token";
import isExpired from './services/isExpired';


export default function Main() {
    const { token, setToken } = useToken();


    if(!token || isExpired()) {
        return <Login setToken={setToken} />
    }

    const userRole = JSON.parse(atob(token.split(".")[1])).roles[0];

    return (
        <div style={{display: "flex", flexDirection: "column", minHeight: "100vh", background: '#E8E8E8'}}>
            {userRole == "ROLE_ADMIN" ?
                <AdminMenu onLogOut={setToken} />
                : userRole == 'ROLE_SUPERVISOR' ? <SupervisorMenu onLogOut={setToken} />
                    : <ProgrammerMenu onLogOut={setToken} />
            }
            <Routes>
                <Route path='/' element={<Test />} />
                <Route path='/auth/login' element={<Login setToken={setToken} />} />
                <Route path='/admin/projects' element={<Projects />} />
                <Route path='/admin/users' element={<Users />} />
                <Route path='/admin/participation' element={<Participation />} />
                <Route path='/user/projects' element={<UserProjects />} />
                <Route path='/supervisor/projects' element={<SupervisorProjects />} />
            </Routes>
            <div className="container-fluid p-1 border-top  text-white d-flex justify-content-center"
                 style={{marginTop: "auto",
                     height: "30px",
                     fontFamily: "Comforter, cursive",
                     backgroundColor: '#348C31'
                 }}>
                <div className="row">
                    <div className="col-12"><b>© 2021 Copyright IFF-8/2 Martynas Maslauskas</b></div>
                </div>
            </div>
        </div>
    )
}