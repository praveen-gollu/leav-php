import { NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LeaveListComponent } from './components/leave-list/leave-list.component';
import { LeaveFormComponent } from './components/leave-form/leave-form.component';

const routes: Routes = [
  { path: '', component: LeaveListComponent },
  { path: 'apply', component: LeaveFormComponent },
  { path: 'edit/:id', component: LeaveFormComponent },
  { path: 'approved', component: LeaveListComponent, data: { status: 'APPROVED' } },
  { path: 'rejected', component: LeaveListComponent, data: { status: 'REJECTED' } }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class LeaveRoutingModule { }