import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { ApiService } from '../../../services/api.service';
import { Company } from '../../../models/company.model';

@Injectable({
  providedIn: 'root'
})
export class CompanyService {
  constructor(private apiService: ApiService) {}

  getCompanies(): Observable<Company[]> {
    return this.apiService.get<Company[]>('/companies');
  }

  getCompany(id: number): Observable<Company> {
    return this.apiService.get<Company>(`/companies/${id}`);
  }

  createCompany(company: Company): Observable<any> {
    return this.apiService.post('/companies', company);
  }

  updateCompany(id: number, company: Company): Observable<any> {
    return this.apiService.put(`/companies/${id}`, company);
  }

  deleteCompany(id: number): Observable<any> {
    return this.apiService.delete(`/companies/${id}`);
  }
}