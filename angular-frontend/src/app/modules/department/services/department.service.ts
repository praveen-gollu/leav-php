import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from '../../../services/api.service';
import { Department } from '../../../models/company.model';

@Injectable({
  providedIn: 'root'
})
export class DepartmentService {
  constructor(private apiService: ApiService) {}

  getDepartments(): Observable<Department[]> {
    return this.apiService.get<Department[]>('/departments');
  }

  getDepartment(id: number): Observable<Department> {
    return this.apiService.get<Department>(`/departments/${id}`);
  }

  createDepartment(department: Department): Observable<any> {
    return this.apiService.post('/departments', department);
  }

  updateDepartment(id: number, department: Department): Observable<any> {
    return this.apiService.put(`/departments/${id}`, department);
  }

  deleteDepartment(id: number): Observable<any> {
    return this.apiService.delete(`/departments/${id}`);
  }
}