import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { LeaveService } from '../../services/leave.service';
import { AuthService } from '../../../../services/auth.service';
import { Leave, LeaveType } from '../../../../models/leave.model';
import { User } from '../../../../models/user.model';

@Component({
  selector: 'app-leave-form',
  templateUrl: './leave-form.component.html',
  styleUrls: ['./leave-form.component.css']
})
export class LeaveFormComponent implements OnInit {
  leaveForm: FormGroup;
  leaveTypes: LeaveType[] = [];
  currentUser: User | null = null;
  isEditMode = false;
  leaveId: number | null = null;
  loading = false;

  constructor(
    private formBuilder: FormBuilder,
    private leaveService: LeaveService,
    private authService: AuthService,
    private router: Router,
    private route: ActivatedRoute
  ) {
    this.leaveForm = this.formBuilder.group({
      EMPLOYID: ['', Validators.required],
      DATESTART: ['', Validators.required],
      DATEEND: ['', Validators.required],
      SHIFTTIME: ['All Day', Validators.required],
      TYPEOFLEAVE: ['', Validators.required],
      REASON: ['', Validators.required],
      LEAVESTATUS: ['PENDING'],
      ADMINREMARKS: ['N/A']
    });
  }

  ngOnInit(): void {
    this.currentUser = this.authService.currentUserValue;
    
    if (this.currentUser) {
      this.leaveForm.patchValue({
        EMPLOYID: this.currentUser.EMPLOYID
      });
    }
    
    this.loadLeaveTypes();
    
    this.route.params.subscribe(params => {
      if (params['id']) {
        this.isEditMode = true;
        this.leaveId = +params['id'];
        this.loadLeave(this.leaveId);
      }
    });
  }

  loadLeaveTypes(): void {
    this.leaveService.getLeaveTypes().subscribe({
      next: (data) => this.leaveTypes = data,
      error: (error) => console.error('Error loading leave types:', error)
    });
  }

  loadLeave(id: number): void {
    this.leaveService.getLeave(id).subscribe({
      next: (leave) => {
        this.leaveForm.patchValue(leave);
      },
      error: (error) => console.error('Error loading leave:', error)
    });
  }

  onSubmit(): void {
    if (this.leaveForm.invalid) {
      return;
    }

    this.loading = true;
    const formData = this.leaveForm.value;

    if (this.isEditMode && this.leaveId) {
      this.leaveService.updateLeave(this.leaveId, formData).subscribe({
        next: () => {
          this.router.navigate(['/leaves']);
        },
        error: (error) => {
          console.error('Error updating leave:', error);
          this.loading = false;
        }
      });
    } else {
      this.leaveService.createLeave(formData).subscribe({
        next: () => {
          this.router.navigate(['/leaves']);
        },
        error: (error) => {
          console.error('Error creating leave:', error);
          this.loading = false;
        }
      });
    }
  }

  isAdmin(): boolean {
    return this.currentUser?.EMPPOSITION === 'Administrator' || 
           this.currentUser?.EMPPOSITION === 'Supervisor user' ||
           this.currentUser?.EMPPOSITION === 'Manager user';
  }
}