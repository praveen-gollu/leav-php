import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from '../../../services/api.service';
import { User } from '../../../models/user.model';

@Injectable({
  providedIn: 'root'
})
export class EmployeeService {
  constructor(private apiService: ApiService) {}

  getEmployees(): Observable<User[]> {
    return this.apiService.get<User[]>('/employees');
  }

  getEmployee(id: number): Observable<User> {
    return this.apiService.get<User>(`/employees/${id}`);
  }

  createEmployee(employee: User): Observable<any> {
    return this.apiService.post('/employees', employee);
  }

  updateEmployee(id: number, employee: User): Observable<any> {
    return this.apiService.put(`/employees/${id}`, employee);
  }

  deleteEmployee(id: number): Observable<any> {
    return this.apiService.delete(`/employees/${id}`);
  }
}