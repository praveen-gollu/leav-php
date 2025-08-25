import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../../services/auth.service';
import { User } from '../../../models/user.model';

@Component({
  selector: 'app-sidebar',
  templateUrl: './sidebar.component.html',
  styleUrls: ['./sidebar.component.css']
})
export class SidebarComponent implements OnInit {
  currentUser: User | null = null;

  constructor(private authService: AuthService) {}

  ngOnInit(): void {
    this.authService.currentUser.subscribe(user => {
      this.currentUser = user;
    });
  }

  isAdmin(): boolean {
    return this.currentUser?.EMPPOSITION === 'Administrator';
  }

  isNormalUser(): boolean {
    return this.currentUser?.EMPPOSITION === 'Normal user';
  }

  isSupervisorOrManager(): boolean {
    return this.currentUser?.EMPPOSITION === 'Supervisor user' || 
           this.currentUser?.EMPPOSITION === 'Manager user';
  }
}