import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from '../../../services/api.service';
import { Leave, LeaveType } from '../../../models/leave.model';

@Injectable({
  providedIn: 'root'
})
export class LeaveService {
  constructor(private apiService: ApiService) {}

  getLeaves(status?: string, employeeId?: string): Observable<Leave[]> {
    let endpoint = '/leaves';
    const params = [];
    
    if (status) {
      params.push(`status=${status}`);
    }
    if (employeeId) {
      params.push(`employee_id=${employeeId}`);
    }
    
    if (params.length > 0) {
      endpoint += '?' + params.join('&');
    }
    
    return this.apiService.get<Leave[]>(endpoint);
  }

  getLeave(id: number): Observable<Leave> {
    return this.apiService.get<Leave>(`/leaves/${id}`);
  }

  createLeave(leave: Leave): Observable<any> {
    return this.apiService.post('/leaves', leave);
  }

  updateLeave(id: number, leave: Leave): Observable<any> {
    return this.apiService.put(`/leaves/${id}`, leave);
  }

  deleteLeave(id: number): Observable<any> {
    return this.apiService.delete(`/leaves/${id}`);
  }

  getLeaveTypes(): Observable<LeaveType[]> {
    return this.apiService.get<LeaveType[]>('/leave-types');
  }
}