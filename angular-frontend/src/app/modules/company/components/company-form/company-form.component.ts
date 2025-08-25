import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { CompanyService } from '../../services/company.service';
import { Company } from '../../../../models/company.model';

@Component({
  selector: 'app-company-form',
  templateUrl: './company-form.component.html',
  styleUrls: ['./company-form.component.css']
})
export class CompanyFormComponent implements OnInit {
  companyForm: FormGroup;
  isEditMode = false;
  companyId: number | null = null;
  loading = false;

  constructor(
    private formBuilder: FormBuilder,
    private companyService: CompanyService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.companyForm = this.formBuilder.group({
      COMPANY: ['', Validators.required]
    });
  }

  ngOnInit(): void {
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.companyId = +params['id'];
        this.loadCompany(this.companyId);
      }
    });
  }

  loadCompany(id: number): void {
    this.companyService.getCompany(id).subscribe({
      next: (company) => {
        this.companyForm.patchValue(company);
      },
      error: (error) => console.error('Error loading company:', error)
    });
  }

  onSubmit(): void {
    if (this.companyForm.invalid) {
      return;
    }

    this.loading = true;
    const formData = this.companyForm.value;

    if (this.isEditMode && this.companyId) {
      this.companyService.updateCompany(this.companyId, formData).subscribe({
        next: () => {
          this.router.navigate(['/companies']);
        },
        error: (error) => {
          console.error('Error updating company:', error);
          this.loading = false;
        }
      });
    } else {
      this.companyService.createCompany(formData).subscribe({
        next: () => {
          this.router.navigate(['/companies']);
        },
        error: (error) => {
          console.error('Error creating company:', error);
          this.loading = false;
        }
      });
    }
  }
}