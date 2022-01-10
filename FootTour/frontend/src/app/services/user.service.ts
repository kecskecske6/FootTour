import { HttpClient } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment.prod';
import { User } from '../models/user';

@Injectable({
  providedIn: 'root'
})
export class UserService {
  user : User | null;

  constructor(private http: HttpClient) {
    this.user = null;
  }
  
  insert(data: any): Observable<User> {
    console.log(data);
    return this.http.post<User>(`${environment.apiURL}/store.php`, data);
  }

  getUserById(id: number): Observable<User>{
    return this.http.get<User>(`${environment.backend}/controllers/userController.php?id=${id}`);
  }

  SetUser(user : User){
    this.user = user;
  }

  getUserId() : number{
    return this.user?.id!;
  }

  logOutUser(){
    this.user = null;
  }
}
