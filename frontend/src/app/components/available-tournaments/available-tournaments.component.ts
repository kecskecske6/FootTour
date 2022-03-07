import { Component, OnInit } from '@angular/core';
import { TournamentModel } from 'src/app/models/Tournament';
import { AuthService } from 'src/app/services/auth.service';
import { TournamentService } from 'src/app/services/tournament.service';
import { UserService } from 'src/app/services/user.service';

@Component({
  selector: 'app-available-tournaments',
  templateUrl: './available-tournaments.component.html',
  styleUrls: ['./available-tournaments.component.sass']
})
export class AvailableTournamentsComponent implements OnInit {

  tournaments: TournamentModel[] = [];
  organizer = '';

  constructor(private tournamentService: TournamentService, private userService: UserService, private auth: AuthService) { }

  ngOnInit(): void {
    this.getTournaments();
    this.getOrganizerName();
  }

  getTournaments(): void {
    this.tournamentService.getAll().subscribe(
      (data: TournamentModel[]) => {
        data.forEach(t => {
          this.tournaments.push(new TournamentModel(t));
        });
      },
      error => {
        console.log(error);
        if (error.status == 401) {
          this.auth.logout();
        }
      }
    );
  }

  getOrganizerName(): void {
    this.userService.getById(Number(this.auth.getId())).subscribe(
      result => this.organizer = result.name,
      error => console.log(error)
    );
  }

}