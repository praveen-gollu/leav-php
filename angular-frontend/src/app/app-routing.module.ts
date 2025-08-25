import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { DashboardComponent } from './components/dashboard/dashboard.component';
import { AuthGuard } from './guards/auth.guard';

const routes: Routes = [
  { path: '', redirectTo: '/login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'dashboard', component: DashboardComponent, canActivate: [AuthGuard] },
  { path: 'employees', loadChildren: () => import('./modules/employee/employee.module').then(m => m.EmployeeModule), canActivate: [AuthGuard] },
  { path: 'leaves', loadChildren: () => import('./modules/leave/leave.module').then(m => m.LeaveModule), canActivate: [AuthGuard] },
  { path: 'companies', loadChildren: () => import('./modules/company/company.module').then(m => m.CompanyModule), canActivate: [AuthGuard] },
  { path: 'departments', loadChildren: () => import('./modules/department/department.module').then(m => m.DepartmentModule), canActivate: [AuthGuard] },
  { path: '**', redirectTo: '/login' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }