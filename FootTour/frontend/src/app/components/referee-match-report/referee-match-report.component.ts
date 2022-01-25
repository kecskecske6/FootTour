import { ThrowStmt } from '@angular/compiler';
import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { Event } from '@angular/router';
import { event } from 'src/app/models/Event';
import { Player } from 'src/app/models/Player';
import { MatchService } from 'src/app/services/match.service';

@Component({
  selector: 'app-referee-match-report',
  templateUrl: './referee-match-report.component.html',
  styleUrls: ['./referee-match-report.component.sass']
})
export class RefereeMatchReportComponent implements OnInit {

  id = 1;
  underModify = {
    team: 1,
    index: -1,
    modifying: false
  }
  minute: number = 1;
  matchreport!: FormGroup;
  team1Name = "";
  team2Name = "";
  team1Goals = 0;
  team2Goals = 0;
  team1Players: Player[] = [];
  team2Players: Player[] = [];
  event: event = new event();
  events: event[] = [];
  
  constructor(private matchService: MatchService) { }

  ngOnInit(): void {
    this.getMatchById();
  }

  getMatchById(){
    this.matchService.getMatchById(this.id).subscribe(
      (result: any)=>{
      console.log(result);
      this.team1Name = result.team1Name.name;
      this.team2Name = result.team2Name.name;
      this.team1Players = this.matchService.setPlayerProperties(result.team1Players);
      this.team2Players = this.matchService.setPlayerProperties(result.team2Players);
    },
    error=>{
      console.log(error);
    });
  }

  onSubmit(){
    console.log(this.matchreport.controls.team1Score.value);
  }

  eventAssign(player: Player, type: string, index: number, team: number){
    if(type == "yellowCard" && !this.underModify.modifying) player.yellow_cards++;
    else if(type == "redCard" && !this.underModify.modifying) player.red_cards++;
    else if(type == "goal" && this.underModify.index != index && !this.underModify.modifying){
      this.underModify.index = index;
      this.underModify.modifying = true;
      this.underModify.team = team;
    }
  }

  saveGoal(player: Player, type: string, teamName: string){
    if(this.minute > 0 && this.minute < 120){
    this.event = new event;
    this.event.match_id = this.id;
    this.event.player_id = player.id;
    this.event.type = type;
    this.event.minute = this.minute;
    player.number_of_goals_in_a_match.push(this.minute);
    if(teamName == this.team1Name) this.team1Goals++;
    else this.team2Goals++;
    this.events.push(this.event);
    console.log(this.events);
    this.stopModify(player);
    }
  }

  deleteGoal(player: Player, index: number, teamName: string){
    player.number_of_goals_in_a_match.splice(index, 1);
    if(teamName == this.team1Name) this.team1Goals--;
    else this.team2Goals--;
  }
  removeCard(player: Player, type: string){
    if(type == "yellowCard") player.yellow_cards--;
    else player.red_cards--;
  }


  stopModify(player: Player){
      // this.underModify.modifiedPlayer = player;
      this.underModify.modifying = false;
      this.underModify.index = -1;
      this.minute = 1;
  }
}
