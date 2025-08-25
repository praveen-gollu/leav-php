import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { EmployeeService } from '../../services/employee.service';
import { CompanyService } from '../../../company/services/company.service';
import { DepartmentService } from '../../../department/services/department.service';
import { User } from '../../../../models/user.model';
import { Company, Department } from '../../../../models/company.model';

@Component({
  selector: 'app-employee-form',
  templateUrl: './employee-form.component.html',
  styleUrls: ['./employee-form.component.css']
})
export class EmployeeFormComponent implements OnInit {
  employeeForm: FormGroup;
  companies: Company[] = [];
  departments: Department[] = [];
  isEditMode = false;
  employeeId: number | null = null;
  loading = false;

  constructor(
    private formBuilder: FormBuilder,
    private employeeService: EmployeeService,
    private companyService: CompanyService,
    private departmentService: DepartmentService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.employeeForm = this.formBuilder.group({
      EMPLOYID: ['', Validators.required],
      EMPNAME: ['', Validators.required],
      EMPSEX: ['MALE', Validators.required],
      USERNAME: ['', [Validators.required, Validators.email]],
      COMPANY: ['', Validators.required],
      DEPARTMENT: ['', Validators.required],
      PASSWRD: ['', Validators.required],
      EMPPOSITION: ['Normal user', Validators.required]
    });
  }

  ngOnInit(): void {
    this.loadCompanies();
    this.loadDepartments();
    
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.employeeId = +params['id'];
        this.loadEmployee(this.employeeId);
        this.employeeForm.get('PASSWRD')?.clearValidators();
        this.employeeForm.get('PASSWRD')?.updateValueAndValidity();
      }
    });
  }

  loadCompanies(): void {
    this.companyService.getCompanies().subscribe({
      next: (data) => this.companies = data,
      error: (error) => console.error('Error loading companies:', error)
    });
  }

  loadDepartments(): void {
    this.departmentService.getDepartments().subscribe({
      next: (data) => this.departments = data,
      error: (error) => console.error('Error loading departments:', error)
    });
  }

  loadEmployee(id: number): void {
    this.employeeService.getEmployee(id).subscribe({
      next: (employee) => {
        this.employeeForm.patchValue(employee);
      },
      error: (error) => console.error('Error loading employee:', error)
    });
  }

  onSubmit(): void {
    if (this.employeeForm.invalid) {
      return;
    }

    this.loading = true;
    const formData = this.employeeForm.value;

    if (this.isEditMode && this.employeeId) {
      this.employeeService.updateEmployee(this.employeeId, formData).subscribe({
        next: () => {
          this.router.navigate(['/employees']);
        },
        error: (error) => {
          console.error('Error updating employee:', error);
          this.loading = false;
        }
      });
    } else {
      this.employeeService.createEmployee(formData).subscribe({
        next: () => {
          this.router.navigate(['/employees']);
        },
        error: (error) => {
          console.error('Error creating employee:', error);
          this.loading = false;
        }
      });
    }
  }
}