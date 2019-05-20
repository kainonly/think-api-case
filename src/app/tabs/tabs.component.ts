import {Component} from '@angular/core';

@Component({
  selector: 'app-tabs',
  templateUrl: './tabs.component.html',
  styleUrls: ['./tabs.component.scss']
})
export class TabsComponent {
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
