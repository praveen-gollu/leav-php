export interface Leave {
  LEAVEID?: number;
  EMPLOYID: string;
  DATESTART: string;
  DATEEND: string;
  NODAYS: number;
  SHIFTTIME: string;
  TYPEOFLEAVE: string;
  REASON: string;
  LEAVESTATUS: string;
  ADMINREMARKS: string;
  DATEPOSTED: string;
}

export interface LeaveType {
  LEAVTID: number;
  LEAVETYPE: string;
  DESCRIPTION: string;
}