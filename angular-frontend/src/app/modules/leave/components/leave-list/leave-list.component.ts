import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { LeaveService } from '../../services/leave.service';
import { AuthService } from '../../../../services/auth.service';
import { Leave } from '../../../../models/leave.model';
import { User } from '../../../../models/user.model';

@Component({
  selector: 'app-leave-list',
  templateUrl: './leave-list.component.html',
  styleUrls: ['./leave-list.component.css']
})
export class LeaveListComponent implements OnInit {
  leaves: Leave[] = [];
  loading = true;
  currentUser: User | null = null;
  status = 'PENDING';

  constructor(
    private leaveService: LeaveService,
    private authService: AuthService,
    private route: ActivatedRoute
  ) {}

  ngOnInit(): void {
    this.currentUser = this.authService.currentUserValue;
    
    this.route.data.subscribe(data => {
      if (data['status']) {
        this.status = data['status'];
      }
    });
    
    this.loadLeaves();
  }

  loadLeaves(): void {
    const employeeId = this.currentUser?.EMPPOSITION === 'Normal user' ? this.currentUser.EMPLOYID : undefined;
    
    this.leaveService.getLeaves(this.status, employeeId).subscribe({
      next: (data) => {
        this.leaves = data;
        this.loading = false;
      },
      error: (error) => {
        console.error('Error loading leaves:', error);
        this.loading = false;
      }
    });
  }

  canEdit(): boolean {
    return this.currentUser?.EMPPOSITION !== 'Normal user';
  }

  canAdd(): boolean {
    return this.currentUser?.EMPPOSITION === 'Administrator';
  }

  getStatusBadgeClass(status: string): string {
    switch(status) {
      case 'APPROVED': return 'badge bg-success';
      case 'REJECTED': return 'badge bg-danger';
      case 'PENDING': return 'badge bg-warning';
      default: return 'badge bg-secondary';
    }
  }
}