import 'react-native-gesture-handler';
import React, { useEffect } from 'react';
import SplashScreen from 'react-native-splash-screen';
import { LogBox } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import AppNavigator from 'navigators/app';
import AuthProvider from 'contexts/AuthContext';

LogBox.ignoreLogs(['VirtualizedLists', 'componentWillReceiveProps']);

const App = () => {
  useEffect(() => {
    SplashScreen.hide();
  }, []);

  return (
    <AuthProvider>
      <NavigationContainer>
        <AppNavigator />
      </NavigationContainer>
    </AuthProvider>
  );
};

export default App;
