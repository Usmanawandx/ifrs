import React, { useContext } from 'react';
import { createNativeStackNavigator } from 'react-native-screens/native-stack';
import Tabs from 'navigators/tabs';
import { enableScreens } from 'react-native-screens';
import Product from 'containers/Product';
import Review from 'containers/Product/Review';
import Checkout from 'containers/Cart/Checkout';
import PaymentResult from 'containers/Cart/Checkout/PaymentResult';
import AddressBook from 'containers/AddressBook';
import NewAddress from 'containers/AddressBook/NewAddress';
import EditAddress from 'containers/AddressBook/EditAddress';
import PaymentMethod from 'containers/PaymentMethod';
import NewCard from 'containers/PaymentMethod/NewCard';
import EditCard from 'containers/PaymentMethod/EditCard';
import Search from 'containers/Search';
import Category from 'containers/Category';
import Shop from 'containers/Shop';
import Orders from 'containers/Orders';
import OrderDetails from 'containers/Orders/Details';
import Notification from 'containers/Notification';
import Pay from 'containers/Pay';
import Chat from 'containers/Chat';
import ChatRoom from 'containers/Chat/ChatRoom';
import Social from 'containers/Social';
import AddReview from 'containers/Review';
import CategoryScreen from 'containers/AllCategory';
import { AuthContext } from 'contexts/AuthContext';
import Auth from '../auth';

enableScreens();
const Stack = createNativeStackNavigator();

const App = () => {
  const { auth: { isLoggedIn } } = useContext(AuthContext);

  return (
    <>
      {isLoggedIn && (
        <>
          <Stack.Navigator
            mode="modal"
            screenOptions={{
              headerShown: false,
            }}
          >
            <Stack.Screen name="Tabs" component={Tabs} />
            <Stack.Screen name="Product" component={Product} />
            <Stack.Screen name="Review" component={Review} />
            <Stack.Screen name="Checkout" component={Checkout} />
            <Stack.Screen name="PaymentResult" component={PaymentResult} />
            <Stack.Screen name="AddressBook" component={AddressBook} />
            <Stack.Screen name="NewAddress" component={NewAddress} />
            <Stack.Screen name="EditAddress" component={EditAddress} />
            <Stack.Screen name="PaymentMethod" component={PaymentMethod} />
            <Stack.Screen name="NewCard" component={NewCard} />
            <Stack.Screen name="EditCard" component={EditCard} />
            <Stack.Screen name="Search" component={Search} />
            <Stack.Screen name="Category" component={Category} />
            <Stack.Screen name="Shop" component={Shop} />
            <Stack.Screen name="Orders" component={Orders} />
            <Stack.Screen name="OrderDetails" component={OrderDetails} />
            <Stack.Screen name="Pay" component={Pay} />
            <Stack.Screen name="Notification" component={Notification} />
            <Stack.Screen name="Chat" component={Chat} />
            <Stack.Screen name="ChatRoom" component={ChatRoom} />
            <Stack.Screen name="Social" component={Social} />
            <Stack.Screen name="AddReview" component={AddReview} />
            <Stack.Screen name="CategoryScreen" component={CategoryScreen}/>


          </Stack.Navigator>
        </>
      )}
      {!isLoggedIn && <Auth />}
    </>
  );
};

export default App;
