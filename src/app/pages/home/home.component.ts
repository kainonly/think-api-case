import {Component} from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  selectedIndex = 0;

  tabBarTabOnPress(pressParam: any) {
    this.selectedIndex = pressParam.index;
  }
}
