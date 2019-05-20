import {Component} from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent {
  title = '服务测试';
  selectedIndex = 0;

  tabBarTabOnPress(pressParam: any) {
    switch (pressParam.index) {
      case 0:
        this.title = '服务测试';
        break;
      case 1:
        this.title = '场景模拟';
        break;
    }
    this.selectedIndex = pressParam.index;
  }
}
