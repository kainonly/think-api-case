import {Component} from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  hidden = false;
  fullScreen = false;
  topFlag = false;
  tintColor = '#108ee9';
  unselectedTintColor = '#888';
  tabbarStyle: any = {height: '400px'};
  selectedIndex = 1;

  showTabBar(event) {
    event.preventDefault();
    this.hidden = !this.hidden;
  }

  showNextTabBar(event) {
    event.preventDefault();
    const PANE_COUNT = 4;
    if (this.selectedIndex === PANE_COUNT - 1) {
      this.selectedIndex = 0;
    } else {
      this.selectedIndex++;
    }
    console.log('selectedIndex: ', this.selectedIndex);
  }

  showFullScreen(event) {
    event.preventDefault();
    this.fullScreen = !this.fullScreen;
    this.tabbarStyle = this.fullScreen
      ? {
        position: 'fixed',
        height: '100%',
        width: '100%',
        top: 0
      }
      : {height: '400px'};
  }

  changePosition(event) {
    event.preventDefault();
    this.topFlag = !this.topFlag;
  }

  tabBarTabOnPress(pressParam: any) {
    console.log('onPress Params: ', pressParam);
    this.selectedIndex = pressParam.index;
  }
}
