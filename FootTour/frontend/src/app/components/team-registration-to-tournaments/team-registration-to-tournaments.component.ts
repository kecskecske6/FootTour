import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Player } from 'src/app/models/Player';
import { TeamService } from 'src/app/services/team.service';
import { AuthService} from 'src/app/services/auth.service';

@Component({
  selector: 'app-team-registration-to-tournaments',
  templateUrl: './team-registration-to-tournaments.component.html',
  styleUrls: ['./team-registration-to-tournaments.component.sass']
})
export class TeamRegistrationToTournamentsComponent implements OnInit {

  players: Player [] = [];
  player: Player = new Player();
  number: number = 0;
  name: string = "";
  teamName: string = "asd";
  tournamentId = 1;
  tournamentName = "Példa Torna"
  underModify = {
    status: false,
    index: -1,
    name: "",
    kitNumber: 0
  }

  constructor(private router: Router,
              private teamService: TeamService,
              private authService: AuthService) {}

  ngOnInit(): void {
  }

  savePlayer(){
    if(this.name != "" && this.number > 0 && this.number < 100 && this.underModify.status == false){
      if(!this.checkDuplicateNumbers()){
        this.player.kit_number = this.number;
        this.player.name = this.name;
        this.players.push(this.player);
        this.player = new Player();
        this.number = 0;
        this.name = "";
        console.log(this.players);
      }
      else{
        console.log("Már létezik ilyen mezszámú játékos!");
      }
    }
    else{
      console.log("Rosszu")
      //TODO helytelen adat alert
    }
  }

  deletePlayer(index: number){
    this.players.splice(index,1);
  }

  checkDuplicateNumbers(){
    if(this.players.some(p => p.kit_number === this.number)) return true;
    return false;
  }

  saveTeamName(teamName: string){
    this.teamName = teamName;
  }

  registerTeam(){
    if(this.players.length > 4)
    {
      const postData = {
        leaderId: this.authService.getId(),
        tournamentId: this.tournamentId,
        teamName: this.teamName,
        players: this.players
      }
      console.log(postData);
      this.teamService.registerTeam(postData).subscribe( 
      result =>{ 
        console.log(result);
      },
      error=>{
        console.log(error);
        if(error.status == 401){
          this.authService.logout();
        }
      }
    );
    }
    else{
      console.log("Nem regisztrált elegendő játékost");
    }
  }

  modify(player: Player){
    this.underModify.status = true;
    this.underModify.name = player.name;
    this.underModify.kitNumber = player.kit_number;
  }

  stopModifyWithSave(index: number, player: Player){
    player.name = this.underModify.name;
    player.kit_number = this.underModify.kitNumber;
    this.players[index] = player;

    this.stopModify();
  }

  stopModify(){
    this.underModify.status = false;
    this.underModify.name = "";
    this.underModify.kitNumber = 0;
  }

}
