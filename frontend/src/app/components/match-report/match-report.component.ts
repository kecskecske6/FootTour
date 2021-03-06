import { Component, OnInit } from '@angular/core';
import { PlayerModel } from 'src/app/models/Player';
import { MatchModel } from 'src/app/models/Match';
import { MatchService } from 'src/app/services/match.service';
import { PlayerService } from 'src/app/services/player.service';
import { UserService } from 'src/app/services/user.service';
import { Event } from 'src/app/interfaces/event';
import { AuthService } from 'src/app/services/auth.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-match-report',
  templateUrl: './match-report.component.html',
  styleUrls: ['./match-report.component.sass']
})
export class MatchReportComponent implements OnInit {
  team1Name= "";
  team2Name = "";
  refereeName = "";
  team1Goals = -1;
  team2Goals = -2;
  tournamentName = "";
  id = 1;
  match!: MatchModel;
  team1Players: PlayerModel[] = [];
  team2Players: PlayerModel[] = [];
  team1PlayersWithEvents: PlayerModel[] = [];
  team2PlayersWithEvents: PlayerModel[] =[];
  events: Event[] = [];

  constructor(private authService: AuthService, private matchService: MatchService, private playerService: PlayerService, private userService: UserService, private router: Router) { }

  ngOnInit(): void {
    this.getMatchById();
  }

  getMatchById(){
    this.matchService.getMatchById(Number(this.router.url.substring(this.router.url.lastIndexOf('/') + 1))).subscribe(
      (result: any) =>{
        console.log(result);
        this.team1Name = result.team1Name.name;
        this.team2Name = result.team2Name.name;
        this.team1Goals = result.team1Goals;
        this.team2Goals = result.team2Goals;
        this.team1Players = result.team1Players;
        this.team2Players = result.team2Players;
        this.refereeName = result.refereeName.name;
        this.tournamentName = result.tournamentName.name;
        this.events = result.events;
        this.team1PlayersWithEvents = this.matchService.setEventsToPlayers(this.events, this.team1Players);
        this.team2PlayersWithEvents = this.matchService.setEventsToPlayers(this.events, this.team2Players)
      },
      error=>{
        console.log(error);
        if(error.status == 401){
          this.authService.logout();
        }
      }
    )
  }

}
