import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {Observable} from 'rxjs';

@Injectable()
export class MainService {
  constructor(private http: HttpClient) {
  }

  /**
   * Token验证
   */
  check(): Observable<any> {
    return this.http.get('/check', {});
  }
}
