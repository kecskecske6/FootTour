import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.prod';
import { User } from '../interfaces/user';
import { UserModel } from '../models/User';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  userName!: string | null;

  constructor(private http: HttpClient) {
  }
  
  insert(data: any): Observable<User> {
    return this.http.post<User>(`${environment.backendURL}/api/users/create.php`, data);
  }

  getById(id: number): Observable<string> {
    return this.http.get<string>(`${environment.backendURL}/api/users/list.php?id=${id}`);
  }

  SetUser(name : string){
    this.userName = name;
  }

  getUserId() : number{
    return Number(localStorage.getItem("id"));
  }

  getName(){
    return localStorage.getItem("name");
  }

  logOutUser(){
   localStorage.clear();
   this.userName = null;
  }

  getTypeOfTheUser(id: number): Observable<string>{
    return this.http.get<string>(`${environment.backendURL}/api/users/getType.php?id=${id}`);
  }

  getByType(type: string): Observable<UserModel[]> {
    return this.http.get<UserModel[]>(`${environment.backendURL}/api/users/list.php?type=${type}`);
  }

  getAllByTournamentId(id: number): Observable<UserModel[]> {
    return this.http.get<UserModel[]>(`${environment.backendURL}/api/users/list.php?tournamentId=${id}`);
  }
}
