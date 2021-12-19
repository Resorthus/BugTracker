import React from 'react';
import bugs from '../styles/bugs.gif';
export default function startPage () {
        return(
            <div className="container-sm mt-1 border rounded" style={{padding: '7px', marginBottom: "50px", background: 'white'}}>
                <h4 style={{borderBottom: '1px solid #ced4da', paddingBottom: '4px'}}>Home</h4>
                <h6>Welcome to Bug Tracking system!</h6><br/>
                <img src={bugs} style={{objectFit: 'contain', width: '100%', height: '100%'}}/>
            </div>
        )
}